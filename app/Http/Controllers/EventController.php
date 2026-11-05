<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelEventRequest;
use App\Http\Requests\DuplicateEventRequest;
use App\Http\Requests\StoreEventRequest;
use App\Models\Client;
use App\Models\Event;
use App\Models\Expense;
use App\Models\User;
use App\Services\NotificationDeliveryService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    public function __construct(private NotificationDeliveryService $notificationDeliveryService) {}

    public function shortages(Request $request): View
    {
        abort_unless($request->user()?->canManageAssignments(), 403);

        $shortageEvents = Event::query()
            ->with(['client', 'assignments'])
            ->whereNotIn('status', ['draft', 'cancelled', 'completed'])
            ->whereDate('event_date', '>=', today())
            ->orderBy('event_date')
            ->get()
            ->filter(fn (Event $event): bool => $event->assignments->where('status', 'confirmed')->count() < $event->required_staff)
            ->values();

        return view('static-pages.Shortage', compact('shortageEvents'));
    }

    public function index(): View
    {
        abort_unless(request()->user()?->canManageAssignments(), 403);

        $eventsQuery = Event::query()
            ->with('client')
            ->withCount(['assignments as confirmed_count' => fn ($query) => $query->where('status', 'confirmed')]);

        $keyword = request()->string('q')->trim()->toString();
        if ($keyword !== '') {
            $eventsQuery->where(fn ($query) => $query
                ->where('name', 'like', "%{$keyword}%")
                ->orWhere('business_name', 'like', "%{$keyword}%"));
        }
        if (request()->filled('client_id')) {
            $eventsQuery->where('client_id', request()->integer('client_id'));
        }

        $status = request()->string('status', 'active')->toString();
        if ($status === 'active') {
            $eventsQuery->whereNotIn('status', ['completed', 'cancelled']);
        } elseif ($status !== 'all') {
            $eventsQuery->where('status', $status);
        }

        $this->applyDateFilter($eventsQuery, request()->string('period', 'month')->toString());
        if (request()->boolean('shortage')) {
            $eventsQuery->whereNotIn('status', ['draft', 'cancelled'])
                ->whereRaw("(select count(*) from assignments where assignments.event_id = events.id and assignments.status = 'confirmed') < events.required_staff");
        }

        return view('static-pages.EventList', [
            'events' => $eventsQuery->orderBy('event_date')->paginate(30)->withQueryString(),
            'clients' => Client::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->isEmployee(), 403);

        return $this->formView();
    }

    public function show(Event $event): View
    {
        $user = request()->user();
        abort_unless(
            $user?->canManageAssignments()
            || ($user !== null && $event->assignments()->where('user_id', $user->id)->exists()),
            403,
        );
        $event->load($this->eventRelations());

        return view('static-pages.EventDetail', $this->viewData($event));
    }

    public function edit(Event $event): View
    {
        abort_unless(request()->user()?->isEmployee(), 403);

        return $this->formView($event);
    }

    public function copy(Event $event): View
    {
        abort_unless(request()->user()?->isEmployee(), 403);
        $event->load($this->eventRelations());

        return view('static-pages.Duplicate', $this->viewData($event));
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $event = DB::transaction(function () use ($request): Event {
            $values = $request->validated();
            $event = Event::query()->create([
                ...$this->eventValues($values),
                'created_by' => $request->user()->id,
            ]);
            $this->synchronizeRelations($event, $values);

            return $event;
        });

        return redirect()->route('events.edit', $event)->with('status', 'イベントを登録しました。');
    }

    public function update(StoreEventRequest $request, Event $event): RedirectResponse
    {
        DB::transaction(function () use ($event, $request): void {
            $values = $request->validated();
            if (in_array($event->status, ['completed', 'cancelled'], true)) {
                $values['status'] = $event->status;
            }
            $event->update($this->eventValues($values));
            $this->synchronizeRelations($event, $values);
        });

        return back()->with('status', 'イベントを保存しました。');
    }

    public function duplicate(DuplicateEventRequest $request, Event $event): RedirectResponse
    {
        $event->load(['reportQuestions', 'invoiceItems', 'expenses']);
        $values = $request->validated();

        $createdEvents = DB::transaction(function () use ($event, $request, $values) {
            return collect($values['dates'])->map(function (string $date) use ($event, $request, $values): Event {
                $eventValues = $event->only([
                    'client_id', 'business_name', 'name', 'details', 'work_content', 'meeting_time', 'start_time',
                    'end_time', 'dismissal_time', 'required_staff', 'meeting_place', 'venue', 'items',
                    'emergency_contacts', 'notes', 'report_required',
                ]);
                $eventValues['event_date'] = $date;
                $eventValues['status'] = $values['status'];
                $eventValues['created_by'] = $request->user()->id;

                if (! $request->boolean('copy_name')) {
                    $eventValues['name'] = null;
                }
                if (! $request->boolean('copy_schedule')) {
                    $eventValues = [...$eventValues, 'meeting_time' => '09:00', 'start_time' => '09:00', 'end_time' => '17:00', 'dismissal_time' => '17:00', 'required_staff' => 1];
                }
                if (! $request->boolean('copy_location')) {
                    $eventValues = [...$eventValues, 'meeting_place' => null, 'venue' => null, 'work_content' => null, 'items' => null, 'emergency_contacts' => null];
                }
                if (! $request->boolean('copy_details')) {
                    $eventValues = [...$eventValues, 'details' => null, 'notes' => null];
                }
                if (! $request->boolean('copy_report')) {
                    $eventValues['report_required'] = false;
                }

                $duplicate = Event::query()->create($eventValues);
                if ($request->boolean('copy_report')) {
                    foreach ($event->reportQuestions as $question) {
                        $duplicate->reportQuestions()->create($question->only(['type', 'question', 'options', 'display_order']));
                    }
                }
                foreach ($event->invoiceItems as $invoiceItem) {
                    $duplicate->invoiceItems()->create($invoiceItem->only(['type', 'memo', 'amount']));
                }

                return $duplicate;
            });
        });

        return redirect()->route('events.edit', $createdEvents->first())->with('status', $createdEvents->count().'件のイベントを複製しました。');
    }

    public function cancel(CancelEventRequest $request, Event $event): RedirectResponse
    {
        $assignments = DB::transaction(function () use ($event) {
            abort_if($event->status === 'completed', 422, '実施済みイベントは中止にできません。');
            $event->update(['status' => 'cancelled']);
            $assignments = $event->assignments()->with('user')->whereIn('status', ['requested', 'unknown', 'accepted', 'confirmed'])->get();
            foreach ($assignments as $assignment) {
                $assignment->update(['status' => 'cancelled', 'responded_at' => now()]);
            }

            return $assignments;
        });

        foreach ($assignments as $assignment) {
            $this->notificationDeliveryService->deliver(
                $assignment->user,
                $event,
                'event_cancelled',
                '取消のお知らせ：'.($event->name ?: $event->business_name),
                ($event->name ?: $event->business_name).'はイベント中止のため、取消となりました。',
            );
        }

        return redirect()->route('events.show', $event)->with('status', 'イベントを中止し、対象アサインをキャンセルしました。');
    }

    public function receipt(Event $event, Expense $expense): StreamedResponse
    {
        abort_unless(request()->user()?->isEmployee(), 403);
        abort_unless($expense->event_id === $event->id && $expense->receipt_path, 404);

        return Storage::disk('local')->download($expense->receipt_path);
    }

    private function formView(?Event $event = null): View
    {
        $event?->load($this->eventRelations());

        return view('static-pages.EventEdit', $this->viewData($event));
    }

    /** @return array<string, mixed> */
    private function viewData(?Event $event): array
    {
        return [
            'event' => $event,
            'events' => $event === null ? collect() : collect([$event]),
            'clients' => Client::query()->orderBy('name')->get(),
            'employees' => User::query()->where('role', 'employee')->where('account_status', 'active')->orderBy('name')->get(),
        ];
    }

    /** @return list<string> */
    private function eventRelations(): array
    {
        return ['client.contacts', 'assignments.user', 'invoiceItems', 'expenses', 'reportQuestions', 'reportSubmissions.user'];
    }

    /** @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    private function eventValues(array $values): array
    {
        $items = $values['items'] ?? [];
        if (in_array('other', $items, true) && filled($values['other_item'] ?? null)) {
            $items[] = 'other:'.$values['other_item'];
        }

        return [
            ...Arr::except($values, ['report_questions', 'invoice_items', 'expenses', 'other_item']),
            'items' => $items,
            'emergency_contacts' => $values['emergency_contacts'] ?? [],
        ];
    }

    /** @param array<string, mixed> $values */
    private function synchronizeRelations(Event $event, array $values): void
    {
        $this->synchronizeSimpleRows($event, 'reportQuestions', $values['report_questions'] ?? [], ['type', 'question', 'options'], true);
        $this->synchronizeSimpleRows($event, 'invoiceItems', $values['invoice_items'] ?? [], ['type', 'memo', 'amount']);
        $this->synchronizeExpenses($event, $values['expenses'] ?? []);
    }

    /** @param list<array<string, mixed>> $rows
     * @param  list<string>  $allowed
     */
    private function synchronizeSimpleRows(Event $event, string $relation, array $rows, array $allowed, bool $ordered = false): void
    {
        $keptIds = [];
        foreach ($rows as $index => $row) {
            $id = Arr::pull($row, 'id');
            $attributes = Arr::only($row, $allowed);
            if ($ordered) {
                $attributes['display_order'] = $index;
            }
            $model = $id === null
                ? $event->{$relation}()->create($attributes)
                : $event->{$relation}()->whereKey($id)->firstOrFail();
            if ($id !== null) {
                $model->update($attributes);
            }
            $keptIds[] = $model->id;
        }

        $event->{$relation}()->when($keptIds !== [], fn ($query) => $query->whereNotIn('id', $keptIds))->delete();
    }

    /** @param list<array<string, mixed>> $expenses */
    private function synchronizeExpenses(Event $event, array $expenses): void
    {
        $keptIds = [];
        foreach ($expenses as $expenseValues) {
            $id = Arr::pull($expenseValues, 'id');
            $receipt = Arr::pull($expenseValues, 'receipt');
            $expense = $id === null ? null : $event->expenses()->whereKey($id)->firstOrFail();
            if ($receipt !== null) {
                if ($expense?->receipt_path) {
                    Storage::disk('local')->delete($expense->receipt_path);
                }
                $expenseValues['receipt_path'] = $receipt->store('expense-receipts', 'local');
            }
            $expense = $expense === null
                ? $event->expenses()->create(Arr::only($expenseValues, ['type', 'memo', 'amount', 'receipt_path']))
                : tap($expense)->update(Arr::only($expenseValues, ['type', 'memo', 'amount', 'receipt_path']));
            $keptIds[] = $expense->id;
        }

        $removedExpenses = $event->expenses()->when($keptIds !== [], fn ($query) => $query->whereNotIn('id', $keptIds))->get();
        foreach ($removedExpenses as $removedExpense) {
            if ($removedExpense->receipt_path) {
                Storage::disk('local')->delete($removedExpense->receipt_path);
            }
            $removedExpense->delete();
        }
    }

    private function applyDateFilter(Builder $query, string $period): void
    {
        [$from, $to] = match ($period) {
            'today' => [today(), today()],
            'week' => [today()->startOfWeek(), today()->endOfWeek()],
            'next_month' => [today()->addMonthNoOverflow()->startOfMonth(), today()->addMonthNoOverflow()->endOfMonth()],
            'custom' => [Carbon::parse(request('from', today())), Carbon::parse(request('to', today()))],
            default => [today()->startOfMonth(), today()->endOfMonth()],
        };

        $query->whereDate('event_date', '>=', $from->toDateString())
            ->whereDate('event_date', '<=', $to->toDateString());
    }
}
