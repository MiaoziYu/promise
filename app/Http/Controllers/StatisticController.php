<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('pages.statistic', compact('user'));
    }
}
