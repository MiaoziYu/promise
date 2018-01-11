<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WeeklyChallengesController extends Controller
{
    public function show($id)
    {
        $weeklyChallenge = auth()->user()->weeklyChallenges()->findOrFail($id);

        return response()->json($weeklyChallenge, 200);
    }

    public function index()
    {
        $weeklyChallenges = auth()->user()->weeklyChallenges()->get();

        return response()->json($weeklyChallenges, 200);
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

        if (request('credits')) {
            $data['credits'] = request('credits');
        }

        if (request('goal')) {
            $data['goal'] = request('goal');
        }

        if (request('failed') and request('failed') === 'false') {
            $data['failed'] = 0;
        }

        auth()->user()->weeklyChallenges()->findOrFail($id)->update($data);

        return response()->json([], 200);
    }

    public function check($id)
    {
        $user = auth()->user();
        $challenge = $user->weeklyChallenges()->findOrFail($id);
        $userProfile = $user->userProfile;
        $bonus = $challenge->goal > $challenge->count ? 1 : 2;

        DB::transaction(function() use ($challenge, $userProfile, $bonus) {
            $challenge->update([
                'count' => $challenge->count + 1
            ]);
            $userProfile->update([
                'credits' => $userProfile->first()->credits + (floor($challenge->credits / $challenge->goal) * $bonus)
            ]);
        });


        return response()->json([], 200);
    }

    public function store()
    {
        auth()->user()->weeklyChallenges()->create([
            'name' => request('name'),
            'description' => request('description'),
            'credits' => request('credits'),
            'goal' => request('goal'),
            'count' => 0,
            'failed' => 0
        ]);

        return response()->json([], 201);
    }

    public function destroy($id)
    {
        auth()->user()->weeklyChallenges()->findOrFail($id)->delete();

        return response()->json([], 200);
    }
}
