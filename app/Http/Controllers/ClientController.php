<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function salesHome(): View
    {
        abort_unless(request()->user()?->isEmployee(), 403);

        $statusCounts = Client::query()
            ->selectRaw('proposal_status, count(*) as aggregate')
            ->groupBy('proposal_status')
            ->pluck('aggregate', 'proposal_status');
        $upcomingActions = Client::query()
            ->whereNotNull('next_action_date')
            ->whereDate('next_action_date', '<=', today()->addDays(14))
            ->orderBy('next_action_date')
            ->limit(10)
            ->get();

        return view('static-pages.SalesHome', [
            'salesStatusCounts' => $statusCounts,
            'upcomingActions' => $upcomingActions,
            'recentMeetings' => Meeting::query()->with('client')->latest('meeting_date')->limit(5)->get(),
            'thisWeekActionCount' => Client::query()->whereBetween('next_action_date', [today()->startOfWeek(), today()->endOfWeek()])->count(),
            'overdueActionCount' => Client::query()->whereDate('next_action_date', '<', today())->count(),
        ]);
    }

    public function proposalBoard(): View
    {
        abort_unless(request()->user()?->isEmployee(), 403);
        $clients = Client::query()
            ->with('salesOwner')
            ->when(request()->filled('keyword'), fn ($query) => $query->where('name', 'like', '%'.request()->string('keyword')->trim().'%'))
            ->when(request()->filled('type'), fn ($query) => $query->where('type', request()->string('type')))
            ->when(request()->filled('status'), fn ($query) => $query->where('proposal_status', request()->string('status')))
            ->orderByRaw('next_action_date is null')
            ->orderBy('next_action_date')
            ->get()
            ->groupBy('proposal_status');

        return view('static-pages.SalesBoard', ['proposalClients' => $clients]);
    }

    public function index(): View
    {
        abort_unless(request()->user()?->canManageAssignments(), 403);

        $query = Client::query()->with(['contacts', 'salesOwner'])->withCount('events');
        $keyword = request()->string('q')->trim()->toString();
        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword): void {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhereHas('contacts', fn ($contactQuery) => $contactQuery->where('name', 'like', "%{$keyword}%"));
            });
        }

        if (request()->filled('type')) {
            $query->where('type', request()->string('type')->toString());
        }
        if (request()->user()->isEmployee() && request()->filled('proposal_status')) {
            $query->where('proposal_status', request()->string('proposal_status')->toString());
        }

        request()->string('sort')->toString() === 'name'
            ? $query->orderBy('name')
            : $query->latest('updated_at');

        return view('static-pages.Clients', ['clients' => $query->paginate(25)->withQueryString()]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->isEmployee(), 403);

        return $this->formView();
    }

    public function edit(Client $client): View
    {
        abort_unless(request()->user()?->isEmployee(), 403);

        return $this->formView($client);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = DB::transaction(function () use ($request): Client {
            $values = $request->validated();
            $client = Client::query()->create(Arr::except($values, 'contacts'));
            $this->synchronizeContacts($client, $values['contacts'] ?? []);

            return $client;
        });

        return redirect()->route('clients.edit', $client)->with('status', '依頼先を登録しました。');
    }

    public function update(StoreClientRequest $request, Client $client): RedirectResponse
    {
        DB::transaction(function () use ($client, $request): void {
            $values = $request->validated();
            if (isset($values['proposal_status']) && $client->proposal_status !== $values['proposal_status']) {
                $values['proposal_status_changed_at'] = now();
            }
            $client->update(Arr::except($values, 'contacts'));
            $this->synchronizeContacts($client, $values['contacts'] ?? []);
        });

        return back()->with('status', '依頼先を保存しました。');
    }

    private function formView(?Client $client = null): View
    {
        $client?->load(['contacts', 'meetings.recorder', 'events']);

        return view('static-pages.ClientEdit', [
            'client' => $client,
            'employees' => User::query()->where('role', 'employee')->where('account_status', 'active')->orderBy('name')->get(),
        ]);
    }

    /** @param list<array<string, mixed>> $contacts */
    private function synchronizeContacts(Client $client, array $contacts): void
    {
        $keptIds = [];
        foreach ($contacts as $contactValues) {
            $contactId = Arr::pull($contactValues, 'id');
            $contact = $contactId === null
                ? $client->contacts()->create($contactValues)
                : $client->contacts()->whereKey($contactId)->firstOrFail();

            if ($contactId !== null) {
                $contact->update($contactValues);
            }
            $keptIds[] = $contact->id;
        }

        $client->contacts()
            ->when($keptIds !== [], fn ($query) => $query->whereNotIn('id', $keptIds))
            ->delete();
    }
}
