<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Resources\Client as ClientResource;
use App\Http\Resources\ClientCollection;

class ClientController extends Controller
{
    public function index()
    {
        return new ClientCollection(Client::all());
    }

    public function show($id)
    {
        return new ClientResource(Client::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $client = Client::create($request->all());

        return (new ClientResource($client))
                ->response()
                ->setStatusCode(201);
    }

    public function answer($id, Request $request)
    {
        $request->merge(['correct' => (bool) json_decode($request->get('correct'))]);
        $request->validate([
            'correct' => 'required|boolean'
        ]);

        $client = Client::findOrFail($id);
        $client->results++;
        $client->win = ($request->get('correct')
                           ? $client->win + 1
                           : $client->win - 1);
        $client->save();

        return new ClientResource($client);
    }

    public function delete($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(null, 204);
    }

    public function resetResults($id)
    {
        $client = Client::findOrFail($id);
        $client->results = 0;
        $client->win = 0;

        return new ClientResource($client);
    }
}
