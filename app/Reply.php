<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reply extends Model
{
	use RecordsActivity;
    use Favoritable;

	protected $guarded = [];
    protected $with = ['owner','favorites'];

    protected $appends = ['favoritesCount','isFavorited'];

    protected static function boot() {

        parent::boot();

        static::deleted(function ($reply) {
            // Delete Replies associated when a thread is deleted
            $reply->favorites->each->delete();
            $reply->thread->decrement('replies_count');
        });

        static::created(function ($reply) {
            // Delete Replies associated when a thread is deleted
            $reply->thread->increment('replies_count');
        });

    }

    public function owner(){
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function mentionedUsers(){
        preg_match_all('/\@([^\s\.]+)/', $this->body, $matches); 
        return $matches[1];
    }

    public function thread() {
    	return $this->belongsTo(Thread::class);
    }

    public function path() {
        return $this->thread->path().'#reply-'.$this->id;
    }

    public function wasJustPublished() {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

}
