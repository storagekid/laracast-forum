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
        'name', 'email', 'password', 'avatar_path', 'confirmation_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','email',
    ];

    protected $casts = [
        'confirmed' => 'boolean',
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

    public function lastReply() {
        return $this->hasOne(Reply::class)->latest();
    }

    public function activities() {

        return $this->hasMany(Activity::class);
    }

    public function profileLink() {

        return '/profiles/'.$this->name;
    }

    public function confirm() {
        $this->confirmed = true;
        $this->confirmation_token = NULL;
        $this->save();
    }

    public function avatar() {
        return $this->avatar_path ?: 'avatars/default.jpg';
    }
    public function getAvatarPathAttribute($avatar) {
        return asset($avatar ?: '/avatars/default.jpg');
    }

    public function visitedCacheKey($item) {
        return sprintf("users.%s.visits.%s", $this->id, $item->id);
    }

    public function readed($item) {
        $key = $this->visitedCacheKey($item);
        cache()->forever($key, Carbon::now());
    }
}
