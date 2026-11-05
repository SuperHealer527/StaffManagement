<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::query()->create($request->validated());

        return back()->with('status', '依頼先を登録しました。');
    }

    public function update(StoreClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return back()->with('status', '依頼先を保存しました。');
    }
}
