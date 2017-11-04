<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','email',
    ];

    public function getRouteKeyName() {

        return 'name';

    }

    public function myThreads() {

        return '/threads?by='.$this->name;
        // return '/threads?by='.$this->name;

    }

    public function threads() {

        return $this->hasMany(Thread::class)->latest();

    }

    public function activities() {

        return $this->hasMany(Activity::class);
    }

    public function profileLink() {

        return '/profiles/'.$this->name;
    }

    public function visitedCacheKey($item) {
        return sprintf("users.%s.visits.%s", $this->id, $item->id);
    }

    public function readed($item) {
        $key = $this->visitedCacheKey($item);
        cache()->forever($key, Carbon::now());
    }
}