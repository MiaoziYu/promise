<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Utils\LootManager;
use Illuminate\Http\Request;

class LootsController extends Controller
{
    public function apply($id)
    {
        $loot = auth()->user()->loots()->findOrFail($id);

        $lootManager = new LootManager();

        $lootManager->apply($loot->type);

        return response()->json([], 200);
    }
}
