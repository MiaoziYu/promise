<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use App\Wish;
use App\WishTicket;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class WishesController extends Controller
{
    public function show($id)
    {
        $wish = auth()->user()->wishes()->findOrFail($id);

        return response()->json($wish, 200);
    }

    public function index()
    {
        $wishes = auth()->user()->wishes()->orderBy('order')->get();

        return response()->json($wishes, 200);
    }

    public function store()
    {
        auth()->user()->wishes()->create([
            'owner' => auth()->user()->id,
            'name' => request('name'),
            'description' => request('description'),
            'credits' => request('credits'),
            'image_link' => request('image_link'),
        ]);

        return response()->json([], 201);
    }

    public function update($id)
    {
        $data = [];

        if (request('name')) {
            $data['name'] = request('name');
        }

        if (request('description')) {
            $data['description'] = request('description');
        }

        if (request('image_link')) {
            $data['image_link'] = request('image_link');
        }

        if (request('credits')) {
            $data['credits'] = request('credits');
        }

        auth()->user()->wishes()->findOrFail($id)->update($data);

        return response()->json([], 200);
    }

    public function destroy($id)
    {
        $wish = auth()->user()->wishes()->findOrFail($id);

        DB::transaction(function() use ($wish) {
            $wish->users()->detach();
            $wish->delete();
        });

        return response()->json([], 200);
    }

    public function purchase($id)
    {
        $user = auth()->user();
        $wish = $user->wishes()->findOrFail($id);

        if ($user->userProfile->credits < $wish->credits) {
            return response()->json(['not enough credits'], 422);
        }

        DB::transaction(function() use ($user,$wish){
            $user->wishTickets()->create([
                'name' => $wish->name,
                'image_link' => $wish->image_link,
            ]);

            $user->userProfile->update([
                'credits' => $user->userProfile->credits - $wish->credits
            ]);
        });

        return response()->json([], 200);
    }

    public function share($id)
    {
        $user = auth()->user();
        $wish = $user->wishes()->findOrFail($id);

        if (request('shared_user_email')) {
            User::where('email', request('shared_user_email'))->first()->wishes()->attach($wish);
        }

        return response()->json([], 200);
    }

    public function contribute($id)
    {
        $user = auth()->user();
        $wish = $user->wishes()->findOrFail($id);
        $credits = request('credits');
        $requiredCredits = $wish->credits - $wish->users()->sum('credits');

        if ($user->userProfile->credits < $credits) {
            return response()->json(['not enough credits'], 422);
        }

        if ($credits > $requiredCredits) {
            $credits = $requiredCredits;
        }

        DB::transaction(function() use ($user, $id, $credits) {
            $user->userProfile->update([
                'credits' => $user->userProfile->credits - $credits
            ]);

            $user->wishes()->updateExistingPivot($id, [
                'credits' => $user->wishes()->findOrFail($id)->pivot->credits + $credits
            ]);
        });

        if ($this->hasResolved($wish)) {
            DB::transaction(function() use ($wish){
                $wishTicket = WishTicket::create([
                    'name' => $wish->name,
                    'image_link' => $wish->image_link,
                ]);
                foreach ($wish->users()->get() as $user) {
                    $user->wishtickets()->attach($wishTicket);
                    $user->wishes()->updateExistingPivot($wish->id, [
                        'credits' => 0
                    ]);
                }
            });
        }

        return response()->json([], 200);
    }

    public function reorder()
    {
        $arr = request()->input();

        auth()->user()->wishes()->get()->map(function($wish) use ($arr) {
            foreach($arr as $item) {
                if (is_array($item)) {
                    if ($item['id'] == $wish->id) {
                        $wish->update([
                            'order' => $item['order']
                        ]);
                    }
                }
            }
        });

        return response()->json([], 200);
    }

    private function hasResolved($wish)
    {
        $credits = $wish->users()->sum('credits');

        return $wish->credits == $credits;
    }
}
