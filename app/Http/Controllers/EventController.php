<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;

class EventController extends Controller
{
    public function store(StoreEventRequest $request): RedirectResponse
    {
        Event::query()->create(array_merge($request->validated(), ['created_by' => $request->user()->id]));

        return back()->with('status', 'イベントを登録しました。');
    }
}
