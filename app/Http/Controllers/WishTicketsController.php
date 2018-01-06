<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WishTicketsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('wish_tickets', compact('user'));
    }
}
