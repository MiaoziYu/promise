<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HabitsController extends Controller
{
    public function index()
    {
        $habits = auth()->user()->habits()->get();

        return response()->json($habits, 200);
    }

    public function show($id)
    {
        $habit = auth()->user()->habits()->findOrFail($id);

        return response()->json($habit, 200);
    }

    public function store()
    {
        auth()->user()->habits()->create([
            'name' => request('name'),
            'description' => request('description'),
            'credits' => request('credits'),
            'count' => 0,
            'streak' => 0,
        ]);

        return response()->json([], 201);
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

        auth()->user()->habits()->findOrFail($id)->update($data);

        return response()->json([], 200);
    }

    public function check($id)
    {
        $user = auth()->user();
        $habit = $user->habits()->findOrFail($id);

        if ($this->hasCheckedToday($habit)) {
            return response()->json([], 422);
        }

        DB::transaction(function () use ($user, $habit) {
            $this->updateHabit($habit);
            $this->updateUserProfile($user, $habit);
        });

        return response()->json([], 200);
    }

    public function destroy($id)
    {
        auth()->user()->habits()->findOrFail($id)->delete();

        return response()->json([], 200);
    }

    private function hasStreak($habit)
    {
        if ($habit->checked_at === null) {
            return true;
        }

        return Carbon::parse($habit->checked_at)->isYesterday();
    }

    private function hasCheckedToday($habit)
    {
        if ($habit->checked_at === null) {
            return false;
        }

        return Carbon::parse($habit->checked_at)->isToday();
    }

    private function updateHabit($habit)
    {
        $data = [
            'count' => $habit->count + 1,
        ];

        if ($this->hasStreak($habit)) {
            $data['streak'] = $habit->streak + 1;
        } else {
            $data['streak'] = 0;
        }

        $data['checked_at'] = Carbon::now();

        $habit->update($data);
    }

    private function updateUserProfile($user, $habit)
    {
        $userProfile = $user->userProfile();

        if ($habit->streak > 7) {
            $credits = $habit->credits * 2;
        } else {
            $credits = $habit->credits;
        }

        $userProfile->update([
            'credits' => $userProfile->first()->credits + $credits
        ]);
    }
}
