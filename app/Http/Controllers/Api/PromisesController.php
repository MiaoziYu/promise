<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            'punch_card_total' => request('punch_card_total'),
            'punch_card_finished' => request('punch_card_finished'),
            'reward_type' => request('reward_type'),
            'reward_name' => request('reward_name'),
            'reward_credits' => request('reward_credits'),
            'reward_image_link' => request('reward_image_link')
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

        if (request('reward_credits') !== null) {
            $data['reward_credits'] = request('reward_credits');
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
            if ($promise->reward_type === 'points') {
                $user->updateCredits($promise->id);
            } else if ($promise->reward_type = 'gift') {
                $user->wishTickets()->create([
                    'name' => $promise->reward_name,
                    'image_link' => $promise->reward_image_link,
                ]);
            }

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

                    $userProfile = auth()->user()->userProfile;
                    $userProfile->update([
                        'credits' => $userProfile->first()->credits - $item->reward_credits
                    ]);
                });
            }

            return $item;
        });
    }
}
