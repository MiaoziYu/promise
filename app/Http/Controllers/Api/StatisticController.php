<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        $statistic = [
            'credits_earned' => $this->getCreditsEarned(),
            'credits_contributed' => $this->getCreditsContributed(),
            'habit_checked' => $this->getHabitsChecked(),
            'max_streak' => $this->getMaxStreak(),
            'weekly_challenge_checked' => $this->getWeeklyChallengeChecked(),
            'weekly_challenge_finished' => $this->getWeeklyChallengeFinised(),
            'weekly_challenge_failed' => $this->getWeeklyChallengeFailed(),
            'promise_finished' => $this->getPromiseFinished(),
            'wish_purchased' => $this->getWishPurchased(),
        ];

        return response()->json($statistic, 200);
    }

    private function getCreditsEarned()
    {
        return auth()->user()->userActivities()
            ->whereIn('name', ['habit_checked', 'weekly_challenge_checked', 'promise_finished'])
            ->sum('value');
    }

    private function getCreditsContributed()
    {
        return auth()->user()->userActivities()
            ->where('name', 'credits_contributed')
            ->sum('value');
    }

    private function getHabitsChecked()
    {
        return auth()->user()->userActivities()
            ->where('name', 'habit_checked')
            ->count();
    }

    private function getMaxStreak()
    {
        return auth()->user()->habits()->max('max_streak');
    }

    private function getWeeklyChallengeChecked()
    {
        return auth()->user()->userActivities()
            ->where('name', 'weekly_challenge_checked')
            ->count();
    }

    private function getWeeklyChallengeFinised()
    {
        return auth()->user()->userActivities()
            ->where('name', 'weekly_challenge_finished')
            ->count();
    }

    private function getWeeklyChallengeFailed()
    {
        return auth()->user()->userActivities()
            ->where('name', 'weekly_challenge_failed')
            ->count();
    }

    private function getPromiseFinished()
    {
        return auth()->user()->userActivities()
            ->where('name', 'promise_finished')
            ->count();
    }

    private function getWishPurchased()
    {
        return auth()->user()->wishTickets()->count();
    }
}
