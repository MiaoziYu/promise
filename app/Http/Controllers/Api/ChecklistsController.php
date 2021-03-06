<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ChecklistsController extends Controller
{
    public function store($promiseId)
    {
        auth()->user()->promises()->findOrFail($promiseId)->checklists()->create([
            'text' => request('text'),
            'status' => false
        ]);

        return response()->json([], 201);
    }

    public function update($promiseId, $checklistId)
    {
        $checklist = [];

        if (request('text') !== null) {
            $checklist['text'] = request('text');
        }

        if (request('status') !== null) {
            $checklist['status'] = request('status');
        }

        auth()->user()->promises()->findOrFail($promiseId)->checklists()->findOrFail($checklistId)->update($checklist);

        return response()->json([], 200);
    }

    public function destroy($promiseId, $checklistId)
    {
        auth()->user()->promises()->findOrFail($promiseId)->checklists()->findOrFail($checklistId)->delete();

        return response()->json([], 200);
    }
}
