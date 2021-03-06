<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WishTicketsController extends Controller
{
    public function index()
    {
        if (request('claimed') === 'true') {
            $wishTickets = auth()->user()->wishTickets()->claimed()->orderBy('created_at', 'desc')->get();
        } else {
            $wishTickets = auth()->user()->wishTickets()->unclaimed()->orderBy('created_at', 'desc')->get();
        }

        $wishTicketsGroup = [];

        foreach ($wishTickets as $ticket) {
            $wishTicketsGroup[$ticket->wish_id][] = $ticket;
        }

        return response()->json($wishTicketsGroup, 200);
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
