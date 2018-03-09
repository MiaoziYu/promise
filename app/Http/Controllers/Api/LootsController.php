<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Utils\LootManager;
use Illuminate\Http\Request;

class LootsController extends Controller
{
    public function index()
    {
        $loots = auth()->user()->loots()->orderBy('drop_rate')->get();

        $lootsGroup = [];

        foreach ($loots as $loot) {
            $lootsGroup[$loot->id][] = $loot;
        }

        return response()->json($lootsGroup, 200);
    }

    public function apply($id)
    {
        $lootManager = new LootManager(auth()->user());

        $lootManager->apply($id, request('target_id'));

        return response()->json([], 200);
    }
}
