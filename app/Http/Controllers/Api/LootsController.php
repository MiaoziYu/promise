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
        $loot = auth()->user()->loots()->findOrFail($id);

        $lootManager = new LootManager();

        $lootManager->apply($loot->type);

        return response()->json([], 200);
    }
}
