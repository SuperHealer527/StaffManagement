<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceController extends Controller
{
    public function payments(Request $request): View
    {
        abort_unless($request->user()?->isEmployee(), 403);

        $query = Assignment::query()
            ->with(['event.client', 'user'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->when($request->filled('year'), fn ($assignmentQuery) => $assignmentQuery->whereHas('event', fn ($eventQuery) => $eventQuery->whereYear('event_date', (int) $request->input('year'))))
            ->when($request->filled('name'), fn ($assignmentQuery) => $assignmentQuery->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$request->string('name')->trim().'%')));

        match ($request->input('status', 'open')) {
            'unconfirmed' => $query->where('payment_status', 'unconfirmed'),
            'waiting' => $query->where('payment_status', 'waiting'),
            'paid' => $query->where('payment_status', 'paid'),
            'all' => null,
            default => $query->whereIn('payment_status', ['unconfirmed', 'waiting']),
        };

        $financeAssignments = $query->get()->sortByDesc(fn (Assignment $assignment) => $assignment->event->event_date)->values();
        $paymentStats = Assignment::query()->whereIn('status', ['confirmed', 'completed'])
            ->when($request->filled('year'), fn ($assignmentQuery) => $assignmentQuery->whereHas('event', fn ($eventQuery) => $eventQuery->whereYear('event_date', (int) $request->input('year'))))
            ->get();

        return view('static-pages.PayManage', compact('financeAssignments', 'paymentStats'));
    }

    public function profit(Request $request): View
    {
        abort_unless($request->user()?->isEmployee(), 403);
        $month = $this->month($request);
        $profitRows = $this->profitRows($month);
        $group = $request->input('group') === 'client' ? 'client' : 'event';

        return view('static-pages.Profit', compact('profitRows', 'month', 'group'));
    }

    public function profitCsv(Request $request): StreamedResponse
    {
        abort_unless($request->user()?->isEmployee(), 403);
        $month = $this->month($request);
        $rows = $this->profitRows($month);

        return response()->streamDownload(function () use ($rows): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['実施日', 'イベント', '依頼先', '請求', '謝金', '交通費', '立替', '経費', '原価', '利益', '利益率']);
            foreach ($rows as $row) {
                fputcsv($output, [$row['date'], $row['event']->name ?: $row['event']->business_name, $row['event']->client->name, $row['invoice'], $row['fee'], $row['transportation'], $row['reimbursement'], $row['expense'], $row['cost'], $row['profit'], $row['margin']]);
            }
            fclose($output);
        }, 'profit-'.$month->format('Y-m').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function month(Request $request): Carbon
    {
        $value = $request->input('month', today()->format('Y-m'));
        abort_unless(is_string($value) && preg_match('/^\d{4}-\d{2}$/', $value) === 1, 422);

        return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
    }

    /** @return Collection<int, array{event: Event, date: string, invoice: int|null, fee: int, transportation: int, reimbursement: int, expense: int, cost: int, profit: int|null, margin: float|null}> */
    private function profitRows(Carbon $month): Collection
    {
        return Event::query()
            ->with(['client', 'invoiceItems', 'expenses', 'assignments' => fn ($query) => $query->whereIn('status', ['confirmed', 'completed'])])
            ->whereBetween('event_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->orderBy('event_date')
            ->get()
            ->map(function (Event $event): array {
                $invoice = $event->invoiceItems->isEmpty() ? null : (int) $event->invoiceItems->sum('amount');
                $fee = (int) $event->assignments->sum('fee');
                $transportation = (int) $event->assignments->sum('transportation');
                $reimbursement = (int) $event->assignments->sum('reimbursement');
                $expense = (int) $event->expenses->sum('amount');
                $cost = $fee + $transportation + $reimbursement + $expense;
                $profit = $invoice === null ? null : $invoice - $cost;

                return [
                    'event' => $event,
                    'date' => $event->event_date->format('Y-m-d'),
                    'invoice' => $invoice,
                    'fee' => $fee,
                    'transportation' => $transportation,
                    'reimbursement' => $reimbursement,
                    'expense' => $expense,
                    'cost' => $cost,
                    'profit' => $profit,
                    'margin' => $invoice > 0 ? round($profit / $invoice * 100, 1) : null,
                ];
            });
    }
}
