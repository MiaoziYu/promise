<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
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
            'name' => request('name'),
            'description' => request('description'),
            'punch_card_total' => request('punch_card_total'),
            'punch_card_finished' => request('punch_card_finished'),
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
        $data = [];

        if (request('name') !== null) {
            $data['name'] = request('name');
        }

        if (request('description') !== null) {
            $data['description'] = request('description');
        }

        if (request('punch_card_total') !== null) {
            $data['punch_card_total'] = request('punch_card_total');
        }

        if (request('punch_card_finished') !== null) {
            $data['punch_card_finished'] = request('punch_card_finished');
        }

        if (request('finished') === 'true') {
            $data['finished_at'] = Carbon::now();
            auth()->user()->updateCredits($id);
        }

        auth()->user()->promises()->findOrFail($id)->update($data);

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
