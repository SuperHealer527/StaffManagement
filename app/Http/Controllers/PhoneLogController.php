<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoneLogRequest;
use App\Models\PhoneLog;
use App\Models\User;
use App\Services\AuditService;
use App\Services\NotificationDeliveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhoneLogController extends Controller
{
    public function __construct(
        private AuditService $auditService,
        private NotificationDeliveryService $notificationDeliveryService,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->canManageAssignments(), 403);
        $phoneLogs = PhoneLog::query()
            ->with(['recipient', 'receiver'])
            ->when($request->input('filter') === 'open', fn ($query) => $query->where('status', 'callback_required'))
            ->when($request->input('filter') === 'mine', fn ($query) => $query->where('recipient_id', $request->user()->id))
            ->when($request->filled('keyword'), fn ($query) => $query->where(fn ($subquery) => $subquery->where('caller_name', 'like', '%'.$request->string('keyword')->trim().'%')->orWhere('company', 'like', '%'.$request->string('keyword')->trim().'%')->orWhere('content', 'like', '%'.$request->string('keyword')->trim().'%')))
            ->latest('called_at')
            ->paginate(30)
            ->withQueryString();
        $recipients = User::query()->whereIn('role', ['employee', 'semi_employee'])->where('account_status', 'active')->orderBy('name')->get();

        return view('static-pages.PhoneLog', compact('phoneLogs', 'recipients'));
    }

    public function store(StorePhoneLogRequest $request): RedirectResponse
    {
        $phoneLog = PhoneLog::query()->create([...$request->validated(), 'received_by' => $request->user()->id]);
        $this->auditService->record($phoneLog, 'create_phone_log');
        $phoneLog->load('recipient');
        if ($phoneLog->recipient?->role === 'semi_employee') {
            $this->notificationDeliveryService->deliver(
                $phoneLog->recipient,
                $phoneLog,
                'phone_message',
                '電話伝言があります',
                sprintf('%s%sから電話がありました。%s', $phoneLog->company ? $phoneLog->company.' ' : '', $phoneLog->caller_name, $phoneLog->content),
                route('phone-logs.index', ['filter' => 'mine']),
                '電話記録簿を開く',
            );
        }

        return back()->with('status', '電話記録を保存しました。');
    }

    public function complete(Request $request, PhoneLog $phoneLog): RedirectResponse
    {
        abort_unless($request->user()?->canManageAssignments(), 403);
        $phoneLog->update(['status' => 'completed']);
        $this->auditService->record($phoneLog, 'complete_phone_log');

        return back()->with('status', '対応済みにしました。');
    }
}
