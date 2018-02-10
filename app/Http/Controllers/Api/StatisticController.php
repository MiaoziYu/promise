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
            'habits_checked' => $this->getHabitsChecked(),
            'max_streak' => $this->getMaxStreak(),
            'weekly_challenges_checked' => $this->getWeeklyChallengesChecked(),
            'weekly_challenges_finished' => $this->getWeeklyChallengesFinished(),
            'weekly_challenges_failed' => $this->getWeeklyChallengesFailed(),
            'promises_finished' => $this->getPromisesFinished(),
            'wishes_purchased' => $this->getWishesPurchased(),
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

    private function getWeeklyChallengesChecked()
    {
        return auth()->user()->userActivities()
            ->where('name', 'weekly_challenge_checked')
            ->count();
    }

    private function getWeeklyChallengesFinished()
    {
        return auth()->user()->userActivities()
            ->where('name', 'weekly_challenge_finished')
            ->count();
    }

    private function getWeeklyChallengesFailed()
    {
        return auth()->user()->userActivities()
            ->where('name', 'weekly_challenge_failed')
            ->count();
    }

    private function getPromisesFinished()
    {
        return auth()->user()->userActivities()
            ->where('name', 'promise_finished')
            ->count();
    }

    private function getWishesPurchased()
    {
        return auth()->user()->wishTickets()->count();
    }
}
