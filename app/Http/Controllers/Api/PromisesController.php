<?php

namespace App\Http\Controllers;

use App\Promise;
use Exception;
use Illuminate\Support\Facades\Auth;

class PromisesController extends Controller
{
    public function index()
    {
        $promises = Auth::user()->promises()->get();

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

        if (request('title')) {
            $requestInput['title'] = request('title');
        }
        if (request('description')) {
            $requestInput['description'] = request('description');
        }
        if (request('check_list_quantity')) {
            $requestInput['check_list_quantity'] = request('check_list_quantity');
        }
        if (request('check_list_finished')) {
            $requestInput['check_list_finished'] = request('check_list_finished');
        }

        $promise = Auth::user()
            ->promises()
            ->findOrFail($id)
            ->update($requestInput);

        return response()->json($promise, 200);
    }
}
