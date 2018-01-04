<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('index', compact('user'));
    }
}
