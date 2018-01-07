<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show()
    {
        /* @var User $user */
        $user = auth()->user()->with('userProfile')->get();

        return response()->json($user, 200);
    }

    public function update()
    {
        $user = auth()->user();
        $userAuthInfo = [];
        $userProfile = [];

        if (request('name') !== null) {
            $userAuthInfo['name'] = request('name');
        }

        if (request('email') !== null) {
            $userAuthInfo['email'] = request('email');
        }

        if (request('credit') !== null) {
            $userProfile['credit'] = request('credit');
        }

        $user->update($userAuthInfo);
        $user->userProfile()->update($userProfile);

        return response()->json([], 200);
    }
}
