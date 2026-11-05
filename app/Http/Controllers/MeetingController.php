<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignInboundEmailRequest;
use App\Http\Requests\StoreMeetingRequest;
use App\Models\Client;
use App\Models\InboundEmail;
use App\Models\Meeting;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->isEmployee(), 403);

        $meetings = Meeting::query()
            ->with(['client', 'recorder'])
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->when($request->filled('keyword'), function ($query) use ($request): void {
                $keyword = '%'.$request->string('keyword')->trim().'%';
                $query->where(fn ($nestedQuery) => $nestedQuery
                    ->where('content', 'like', $keyword)
                    ->orWhere('participants', 'like', $keyword)
                    ->orWhereHas('client', fn ($clientQuery) => $clientQuery->where('name', 'like', $keyword)));
            })
            ->when($request->input('period', 'three_months') === 'three_months', fn ($query) => $query->whereDate('meeting_date', '>=', today()->subMonths(3)))
            ->when($request->input('period', 'three_months') === 'year', fn ($query) => $query->whereYear('meeting_date', today()->year))
            ->latest('meeting_date')
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('static-pages.Meetings', [
            'meetingsPage' => $meetings,
            'pendingEmails' => InboundEmail::query()->where('status', 'pending')->latest('received_at')->get(),
            'clients' => Client::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function assignInbound(AssignInboundEmailRequest $request, InboundEmail $inboundEmail): RedirectResponse
    {
        $client = Client::query()->findOrFail($request->integer('client_id'));

        DB::transaction(function () use ($request, $inboundEmail, $client): void {
            $lockedEmail = InboundEmail::query()->lockForUpdate()->findOrFail($inboundEmail->id);
            abort_unless($lockedEmail->status === 'pending' && $lockedEmail->meeting_id === null, 409, 'This email has already been assigned.');

            $meeting = Meeting::query()->create([
                'client_id' => $client->id,
                'recorded_by' => $request->user()->id,
                'type' => 'sales',
                'meeting_date' => $lockedEmail->received_at->toDateString(),
                'method' => 'email',
                'participants' => $lockedEmail->sender_email,
                'content' => trim($lockedEmail->subject."\n".$lockedEmail->body_excerpt),
            ]);
            $lockedEmail->update([
                'client_id' => $client->id,
                'meeting_id' => $meeting->id,
                'status' => 'matched',
            ]);

            $this->auditService->record($lockedEmail, 'inbound_email_assigned', ['status' => 'pending'], [
                'status' => 'matched',
                'client_id' => $client->id,
            ]);
        });

        return back()->with('status', 'メールを依頼先へ振り分けました。');
    }

    public function store(StoreMeetingRequest $request, Client $client): RedirectResponse
    {
        $client->meetings()->create([
            ...$request->validated(),
            'recorded_by' => $request->user()->id,
        ]);

        return back()->with('status', '打合せを登録しました。');
    }

    public function update(StoreMeetingRequest $request, Client $client, Meeting $meeting): RedirectResponse
    {
        abort_unless($meeting->client_id === $client->id, 404);
        $meeting->update($request->validated());

        return back()->with('status', '打合せを更新しました。');
    }

    public function destroy(Client $client, Meeting $meeting): RedirectResponse
    {
        abort_unless(request()->user()?->isEmployee(), 403);
        abort_unless($meeting->client_id === $client->id, 404);
        $meeting->delete();

        return back()->with('status', '打合せを削除しました。');
    }
}
