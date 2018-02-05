<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeeklyChallengesController extends Controller
{
    public function show($id)
    {
        $weeklyChallenge = auth()->user()->weeklyChallenges()->findOrFail($id);

        return response()->json($weeklyChallenge, 200);
    }

    public function index()
    {
        $weeklyChallenges = auth()->user()->weeklyChallenges()->orderBy('order')->get();

        return response()->json($weeklyChallenges, 200);
    }

    public function update($id)
    {
        $data = [];

        if (request('name')) {
            $data['name'] = request('name');
        }

        $data['description'] = request('description');

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
        $bonus = $challenge->goal > $challenge->count ? 1 : 2;

        DB::transaction(function() use ($user, $challenge, $bonus) {
            $challenge->update([
                'count' => $challenge->count + 1
            ]);

            $creditsEarned = (floor($challenge->credits / $challenge->goal) * $bonus);
            $user->userProfile->update([
                'credits' => $user->userProfile->credits + $creditsEarned,
                'credits_earned' => $user->userProfile->credits_earned + $creditsEarned,
            ]);
        });

        return response()->json([], 200);
    }

    public function reorder()
    {
        $arr = request()->input();

        auth()->user()->WeeklyChallenges()->get()->map(function($challenge) use ($arr) {
            foreach($arr as $item) {
                if (is_array($item)) {
                    if ($item['id'] == $challenge->id) {
                        $challenge->update([
                            'order' => $item['order']
                        ]);
                    }
                }
            }
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
