<?php

namespace App\Http\Controllers\Api;

use App\Events\UserActed;
use App\Http\Controllers\Controller;
use App\Promise;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PromisesController extends Controller
{
    public function index()
    {
        if (request('finished') === 'true') {
            $promises = auth()->user()->promises()->with('checklists')->finished()->unexpired()->orderBy('order')->get();
        } elseif (request('finished') === 'false') {
            $promises = auth()->user()->promises()->with('checklists')->unfinished()->unexpired()->orderBy('order')->get();
        } else {
            $promises = auth()->user()->promises()->with('checklists')->orderBy('order')->get();
        }

        $promises = $this->checkDueDate($promises);

        return response()->json($promises, 200);
    }

    public function show($id)
    {
        $promise = auth()->user()->promises()->with('checklists')->findOrFail($id);

        return response()->json($promise, 200);
    }

    public function store()
    {
        $promise = [
            'name' => request('name'),
            'description' => request('description'),
            'credits' => request('credits'),
        ];

        try {
            auth()->user()->promises()->create($promise);
            $response = [];
            $responseCode = 201;
        } catch (Exception $e) {
            $response = $e->getMessage();
            $responseCode = 422;
        }

        return response()->json($response, $responseCode);
    }

    public function update($id)
    {
        $data = [];

        if (request('name') !== null) {
            $data['name'] = request('name');
        }

        $data['description'] = request('description');

        if (request('credits') !== null) {
            $data['credits'] = request('credits');
        }

        if (request('punch_card_total') !== null) {
            $data['punch_card_total'] = request('punch_card_total');
        }

        if (request('punch_card_finished') !== null) {
            $data['punch_card_finished'] = request('punch_card_finished');
        }

        if (request('due_date') !== null) {
            $data['due_date'] = request('due_date');
        }

        if (request('expired') !== null) {
            $data['expired'] = request('expired');
        }

        auth()->user()->promises()->findOrFail($id)->update($data);

        return response()->json([], 200);
    }

    public function finish($id)
    {
        $user = auth()->user();
        $promise = $user->promises()->findOrFail($id);

        DB::transaction(function () use ($user, $promise) {
            $promise->update([
                'finished_at' => Carbon::now()
            ]);

            $user->userProfile->update([
                'credits' => $user->userProfile->credits + $promise->credits,
                'credits_earned' => $user->userProfile->credits_earned + $promise->credits,
                'promises_finished' => $user->userProfile->promises_finished + 1
            ]);

            event(new UserActed([
                'user_id' => $user->id,
                'subject_id' => $promise->id,
                'subject_type' => Promise::class,
                'name' => 'promise_finished',
                'value' => $promise->credits,
            ]));
        });

        return response()->json([], 200);
    }

    public function reorder()
    {
        $arr = request()->input();

        auth()->user()->promises()->get()->map(function($promise) use ($arr) {
            foreach($arr as $item) {
                if (is_array($item)) {
                    if ($item['id'] == $promise->id) {
                        $promise->update([
                            'order' => $item['order']
                        ]);
                    }
                }
            }
        });

        return response()->json([], 200);
    }

    public function destroy($id)
    {
        $promise = auth()->user()->promises()->findOrFail($id);

        DB::transaction(function () use ($promise) {
            $promise->checklists()->delete();
            $promise->delete();
        });

        return response()->json([], 200);
    }

    private function checkDueDate($promises)
    {
        return collect($promises)->map(function($item) {
            if (Carbon::parse($item->due_date)->isYesterday()) {

                DB::transaction(function () use ($item) {
                    $item->update([
                        'expired' => 'pending'
                    ]);

                    auth()->user()->userProfile->update([
                        'credits' => auth()->user()->userProfile->credits - $item->credits
                    ]);
                });
            }

            return $item;
        });
    }
}
