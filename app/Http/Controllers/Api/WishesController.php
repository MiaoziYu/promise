<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class WishesController extends Controller
{
    public function show($id)
    {
        $wish = auth()->user()->wishes()->findOrFail($id);

        return response()->json($wish, 200);
    }

    public function index()
    {
        $wishes = auth()->user()->wishes()->get();

        return response()->json($wishes, 200);
    }

    public function store()
    {
        $wish = [
          'name' => request('name'),
          'description' => request('description'),
          'credits' => request('credits'),
          'image_link' => request('image_link'),
        ];

        try {
            auth()->user()->wishes()->create($wish);
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

        if (request('name')) {
            $data['name'] = request('name');
        }

        if (request('description')) {
            $data['description'] = request('description');
        }

        if (request('image_link')) {
            $data['image_link'] = request('image_link');
        }

        auth()->user()->wishes()->findOrFail($id)->update($data);

        return response()->json([], 200);
    }

    public function destroy($id)
    {
        auth()->user()->wishes()->findOrFail($id)->delete();

        return response()->json([], 200);
    }

    public function purchase($id)
    {
        $user = auth()->user();
        $userProfile = $user->userProfile();
        $wish = $user->wishes()->findOrFail($id);

        if ($userProfile->first()->credits < $wish->credits) {
            return response()->json([], 422);
        }

        DB::transaction(function() use ($userProfile, $wish){
            $wish->update([
                'purchased_at' => Carbon::now()
            ]);
            $userProfile->update([
                'credits' => $userProfile->first()->credits - $wish->credits
            ]);
        });

        return response()->json([], 200);
    }
}
