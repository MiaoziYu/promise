<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        //credits earned, credits contributed, habits checked, max streak, challenge finished, challenge failed, promise finished, wish purchased, most purchased ticket
        $statistic = [
            'credits_earned' => $this->getCreditsEarned()
        ];

        return response()->json($statistic, 200);
    }

    private function getCreditsEarned()
    {
        return auth()->user()->UserActivities()
            ->whereIn('name', ['habit_checked', 'weekly_challenge_checked', 'promise_finished'])
            ->sum('value');
    }
}
