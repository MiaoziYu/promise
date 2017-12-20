<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function promises()
    {
        return $this->hasMany('App\Promise');
    }

    public function checklists()
    {
        return $this->hasMany('App\Checklist');
    }

    public function createPromise($promiseData, $checklistsData)
    {
        /** @var Promise $promise */
        $promise = $this->promises()->create($promiseData);

        if ($checklistsData !== null) {
            foreach($checklistsData as $checklist) {
                $promise->createChecklist($checklist['text']);
            }
        }

        return $promise;
    }

    public function deletePromise($id)
    {
        $promise = $this->promises()->findOrFail($id);

        DB::transaction(function () use ($promise) {
            $promise->checklists()->delete();
            $promise->delete();
        });

    }
}
