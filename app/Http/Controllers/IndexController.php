<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        return view('index', compact('user'));
    }
}
