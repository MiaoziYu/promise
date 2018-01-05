<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WishTicketsController extends Controller
{
    public function index()
    {
        $wishTickets = auth()->user()->wishTickets()->unused()->get();

        return response()->json($wishTickets, 200);
    }

    public function update($id)
    {
        auth()->user()->wishTickets()->findOrFail($id)->update([
            'used_at' => Carbon::now()
        ]);
    }

    public function destroy($id)
    {
        auth()->user()->wishTickets()->findOrFail($id)->delete();

        return response()->json([], 200);
    }
}
