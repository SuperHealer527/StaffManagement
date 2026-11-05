<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\StoreQualificationRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Qualification;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->canManageAssignments(), 403);

        $staffMembers = User::query()
            ->whereIn('role', ['employee', 'semi_employee', 'staff', 'temporary', 'trainer'])
            ->with(['qualifications' => fn ($query) => $query->orderBy('expires_at')])
            ->withCount(['assignments as year_work_count' => fn ($query) => $query
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereHas('event', fn (Builder $eventQuery) => $eventQuery->whereYear('event_date', today()->year))])
            ->when($request->filled('keyword'), fn ($query) => $query->where('name', 'like', '%'.$request->string('keyword')->trim().'%'))
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->when($request->boolean('student'), fn ($query) => $query->where('is_student', true))
            ->when($request->boolean('has_car'), fn ($query) => $query->where('has_car', true))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $expiringCount = User::query()->whereHas('qualifications', fn ($query) => $query
            ->whereBetween('expires_at', [today(), today()->addDays(60)]))->count();

        return view('static-pages.StaffList', compact('staffMembers', 'expiringCount'));
    }

    public function show(Request $request, User $staff): View
    {
        abort_unless($request->user()?->canManageAssignments(), 403);
        abort_unless(in_array($staff->role, ['employee', 'semi_employee', 'staff', 'temporary', 'trainer'], true), 404);

        $staff->load(['qualifications', 'availabilities', 'assignments.event.client']);
        $this->auditService->record($staff, $request->user()->isEmployee() ? 'view_sensitive_staff_profile' : 'view_staff_profile');

        return view('static-pages.StaffDetail', ['staffMember' => $staff, 'dynamicStaffDetail' => true]);
    }

    public function profile(Request $request): View
    {
        $staffMember = $request->user()->load(['qualifications', 'availabilities']);

        return view('static-pages.Profile', ['staffMember' => $staffMember, 'dynamicProfile' => true]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $staff = $request->user();
        $values = $request->validated();
        $oldValues = $staff->only(['name', 'gender', 'birth_date', 'is_student', 'nearest_station', 'postal_code', 'address', 'building', 'phone', 'email', 'secondary_email', 'notification_method', 'has_car', 'car_number']);
        $bankDetails = collect($values)->only(['bank_name', 'bank_code', 'branch_name', 'branch_code', 'account_type', 'account_number', 'account_name'])->filter(fn ($value) => filled($value))->all();

        DB::transaction(function () use ($staff, $values, $bankDetails): void {
            $staff->update([
                ...Arr::only($values, ['name', 'gender', 'birth_date', 'nearest_station', 'postal_code', 'address', 'building', 'phone', 'email', 'secondary_email', 'notification_method', 'car_number']),
                'is_student' => (bool) ($values['is_student'] ?? false),
                'has_car' => (bool) ($values['has_car'] ?? false),
                'bank_details' => $bankDetails ?: null,
            ]);

            foreach ($values['availabilities'] ?? [] as $key => $availability) {
                [$weekday, $timeSlot] = explode('|', $key, 2);
                $staff->availabilities()->updateOrCreate(
                    ['weekday' => (int) $weekday, 'time_slot' => $timeSlot],
                    ['availability' => $availability],
                );
            }
        });

        $this->auditService->record($staff, 'update_own_profile', $oldValues, $staff->fresh()->only(array_keys($oldValues)));

        return back()->with('status', 'プロフィールを更新しました。');
    }

    public function update(UpdateStaffRequest $request, User $staff): RedirectResponse
    {
        $oldValues = $staff->only(['base_fee', 'account_status']);
        $staff->update($request->validated());
        $this->auditService->record($staff, 'update_staff_management', $oldValues, $staff->only(['base_fee', 'account_status']));

        return back()->with('status', 'スタッフ管理情報を更新しました。');
    }

    public function storeQualification(StoreQualificationRequest $request, User $staff): RedirectResponse
    {
        $path = $request->file('certificate')?->store('private/qualifications');
        $qualification = $staff->qualifications()->create([
            'name' => $request->validated('name'),
            'expires_at' => $request->validated('expires_at'),
            'certificate_path' => $path,
        ]);
        $this->auditService->record($qualification, 'create_qualification', null, $qualification->only(['name', 'expires_at']));

        return back()->with('status', '資格を追加しました。');
    }

    public function destroyQualification(Request $request, User $staff, Qualification $qualification): RedirectResponse
    {
        abort_unless($request->user()?->isEmployee() || $request->user()?->is($staff), 403);
        abort_unless($qualification->user_id === $staff->id, 404);
        $oldValues = $qualification->only(['name', 'expires_at']);
        if ($qualification->certificate_path) {
            Storage::delete($qualification->certificate_path);
        }
        $this->auditService->record($qualification, 'delete_qualification', $oldValues);
        $qualification->delete();

        return back()->with('status', '資格を削除しました。');
    }

    public function qualificationFile(Request $request, User $staff, Qualification $qualification): StreamedResponse
    {
        abort_unless($request->user()?->canManageAssignments() || $request->user()?->is($staff), 403);
        abort_unless($qualification->user_id === $staff->id && $qualification->certificate_path, 404);
        $this->auditService->record($qualification, 'download_qualification_certificate');

        return Storage::download($qualification->certificate_path, basename($qualification->certificate_path));
    }

    public function storeContract(StoreContractRequest $request, User $staff): RedirectResponse
    {
        if ($staff->contract_path) {
            Storage::delete($staff->contract_path);
        }
        $oldPath = $staff->contract_path;
        $staff->update(['contract_path' => $request->file('contract')->store('private/contracts')]);
        $this->auditService->record($staff, 'replace_contract', ['contract_path' => $oldPath], ['contract_path' => $staff->contract_path]);

        return back()->with('status', '契約書を保存しました。');
    }

    public function contractFile(Request $request, User $staff): StreamedResponse
    {
        abort_unless($request->user()?->isEmployee() || $request->user()?->is($staff), 403);
        abort_unless($staff->contract_path, 404);
        $this->auditService->record($staff, 'download_contract');

        return Storage::download($staff->contract_path, basename($staff->contract_path));
    }
}
