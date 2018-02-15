<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reply extends Model
{
	use RecordsActivity;
    use Favoritable;

	protected $guarded = [];
    protected $with = ['owner','favorites','thread'];

    protected $appends = ['favoritesCount','isFavorited','isBest'];

    protected static function boot() {

        parent::boot();

        static::deleted(function ($reply) {
            // Delete Replies associated when a thread is deleted
            $reply->favorites->each->delete();
            $reply->thread->decrement('replies_count');
            // If it was marked as best reply in the thread restart thread column to null
            // if ($reply->isBest()) {
            //     $reply->thread->update(['best_reply_id' => null]);
            // }
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
        preg_match_all('/@([\w\-]+)/', $this->body, $matches); 
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

    public function setBodyAttribute($body) {
        $this->attributes['body'] = preg_replace('/@([\w\-]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    public function isBest() {
        return $this->thread->best_reply_id == $this->id;
    }

    public function getIsBestAttribute() {
        return $this->isBest(); 
    }

}
