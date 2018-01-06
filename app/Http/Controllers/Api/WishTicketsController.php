<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WishTicketsController extends Controller
{
    public function index()
    {
        $wishTickets = auth()->user()->wishTickets()->unclaimed()->get();

        return response()->json($wishTickets, 200);
    }

    public function claim($id)
    {
        auth()->user()->wishTickets()->findOrFail($id)->update([
            'claimed_at' => Carbon::now()
        ]);

        return response()->json([], 200);
    }

    public function destroy($id)
    {
        auth()->user()->wishTickets()->findOrFail($id)->delete();

        return response()->json([], 200);
    }
}
