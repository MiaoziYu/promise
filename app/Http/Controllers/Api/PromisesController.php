<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Promise;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class PromisesController extends Controller
{
    public function index()
    {
        if (request('finished') === 'true') {
            $promises = Auth::user()->promises()->finished()->get();
        } elseif (request('finished') === 'false') {
            $promises = Auth::user()->promises()->unfinished()->get();
        } else {
            $promises = Auth::user()->promises()->get();
        }

        return response()->json($promises, 200);
    }

    public function show($id)
    {
        $promise = Auth::user()->promises()->findOrFail($id);

        return response()->json($promise, 200);
    }

    public function store()
    {
        try {
            $response = Promise::create([
                'user_id' => Auth::id(),
                'title' => request('title'),
                'description' => request('description'),
                'check_list_quantity' => request('check_list_quantity'),
                'check_list_finished' => 0,
                'finished_at' => null,
            ]);
            $responseCode = 201;
        } catch (Exception $e) {
            $response = $e->getMessage();
            $responseCode = 422;
        }

        return response()->json($response, $responseCode);
    }

    public function update($id)
    {
        $requestInput = [];
        $promise = Auth::user()->promises()->findOrFail($id);

        if (request('title') !== null) {
            $requestInput['title'] = request('title');
        }
        if (request('description') !== null) {
            $requestInput['description'] = request('description');
        }
        if (request('check_list_quantity') !== null) {
            $requestInput['check_list_quantity'] = request('check_list_quantity');
        }
        if (request('check_list_finished') !== null) {
            $requestInput['check_list_finished'] = request('check_list_finished');
            if (request('check_list_finished') === $promise->check_list_quantity) {
                $requestInput['finished_at'] = Carbon::now();
            }
        }

        $promise->update($requestInput);

        return response()->json($promise, 200);
    }

    public function destroy($id)
    {
        $promise = Auth::user()->promises()->findOrFail($id);

        $promise->delete();

        return response()->json([], 200);
    }
}
