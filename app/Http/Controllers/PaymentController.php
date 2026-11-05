<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Assignment;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function update(UpdatePaymentRequest $request, Assignment $assignment): RedirectResponse
    {
        abort_unless(in_array($assignment->status, ['confirmed', 'completed'], true), 422);
        $assignment->update($request->validated());

        return back()->with('status', '支払状況を更新しました。');
    }
}
