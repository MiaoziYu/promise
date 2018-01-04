<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WishesController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('wishes', compact('user'));
    }
}
