<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Assignment;
use Illuminate\Http\RedirectResponse;

class AssignmentController extends Controller
{
    public function update(UpdateAssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $data = $request->validated();
        if (! $request->user()->isEmployee()) {
            unset($data['fee'], $data['transportation']);
        }
        $assignment->update($data);

        return back()->with('status', 'アサインを更新しました。');
    }
}
