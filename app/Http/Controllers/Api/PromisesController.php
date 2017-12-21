<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;

class PromisesController extends Controller
{
    public function index()
    {
        if (request('finished') === 'true') {
            $promises = auth()->user()->promises()->with('checklists')->finished()->get();
        } elseif (request('finished') === 'false') {
            $promises = auth()->user()->promises()->with('checklists')->unfinished()->get();
        } else {
            $promises = auth()->user()->promises()->with('checklists')->get();
        }

        return response()->json($promises, 200);
    }

    public function show($id)
    {
        $promise = auth()->user()->promises()->with('checklists')->findOrFail($id);

        return response()->json($promise, 200);
    }

    public function store()
    {
        $promise = [
            'title' => request('title'),
            'description' => request('description'),
            'reward_type' => request('reward_type'),
            'reward_content' => request('reward_content')
        ];

        try {
            auth()->user()->promises()->create($promise);
            $response = [];
            $responseCode = 201;
        } catch (Exception $e) {
            $response = $e->getMessage();
            $responseCode = 422;
        }

        return response()->json($response, $responseCode);
    }

    public function update($id)
    {
        $promise = [];

        if (request('title') !== null) {
            $promise['title'] = request('title');
        }

        if (request('description') !== null) {
            $promise['description'] = request('description');
        }

        auth()->user()->promises()->findOrFail($id)->update($promise);

        return response()->json([], 200);
    }

    public function destroy($id)
    {
        $promise = auth()->user()->promises()->findOrFail($id);

        DB::transaction(function () use ($promise) {
            $promise->checklists()->delete();
            $promise->delete();
        });

        return response()->json([], 200);
    }
}
