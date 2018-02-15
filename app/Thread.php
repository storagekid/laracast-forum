<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\ThreadWasUpdated;
use App\Events\ThreadHasNewReply;
use App\Reply;

class Thread extends Model
{
    use RecordsActivity;

	protected $guarded = [];

    protected $with = ['channel','subscriptions'];
    protected $appends = ['isSubscribeTo'];

    protected $casts = ['locked' => 'boolean'];

    protected static function boot() {

        parent::boot();
        // static::addGlobalScope('replyCount', function($builder) {
        //     $builder->withCount('replies');
        // });

        static::addGlobalScope('user', function($builder) {
            $builder->with('user');
        });

        static::deleting(function ($thread) {
            // Delete Replies associated when a thread is deleted
            $thread->replies->each->delete();
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);
        });
    }

    public function getRouteKeyName() {
        return 'slug';
    }

    public function path(){
    	return '/threads/' . $this->channel->slug .'/'.$this->slug;
    }

    public function replies(){
    	return $this->hasMany(Reply::class);
    }


    public function user() {
    	return $this->belongsTo(User::class);
    }

    public function creatorName() {
    	return $this->user->name;
    }

    public function addReply($reply) {
    	$reply = $this->replies()->create($reply);
        // $this->notifySubscribers($reply);
        event(new ThreadHasNewReply($this, $reply));
        return $reply;
    }

    // public function notifySubscribers($reply) {
    //     $this->subscriptions
    //         ->where('user_id', '!=', $reply->user_id )
    //         ->each
    //         ->notify($reply);
    // }

    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    public function scopeFilter($query, $filters) {
        return $filters->apply($query);
    }

    public function subscribe($userId = null) {
        $this->subscriptions()->create([
            'user_id' => $userId ? : auth()->id(),
        ]);
        return $this;
    }

    public function unsubscribe($userId = null) {
        $this->subscriptions()
             ->where(['user_id'=>$userId ? : auth()->id()])
             ->delete();
    }

    public function subscriptions() {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribeToAttribute() {
        return $this->subscriptions()
                    ->where('user_id',auth()->id())
                    ->exists();
    }

    public function hasUpdatesFor() {
        if (!auth()->check()) return false;
        // $key = sprintf("users.%s.visits.%s", auth()->id(), $this->id);
        $key = auth()->user()->visitedCacheKey($this);
        return $this->updated_at > cache($key);
    }
    // Visits Counter With Redis
    // public function visits() {
    //     return new Visits($this);
    // }

    public function visitsFromTrending() {
        $trendings = Trending::doJson('trending_threads');
        foreach($trendings as $thread) {
            if ($thread->title == $this->title) return $thread->score;
        }
    }

    public function setBestReply(Reply $reply) {
        $this->update(['best_reply_id' => $reply->id]);
    }

    public function setSlugAttribute($value) {
        $slug = str_slug($value);

        if (static::whereSlug($slug)->exists()) {
            $slug = $slug."-".$this->id;
        }
        $this->attributes['slug'] = $slug;
        // if (static::whereSlug($slug = str_slug($value))->exists()) {
        //     $slug = $this->incrementSlug($slug);
        // }
        // $this->attributes['slug'] = $slug;
    }
    // public function incrementSlug($slug) {
    //     $max = static::whereTitle($this->title)->latest('id')->value('slug');
    //     if (str_slug($this->title) !== $max) {
    //         return preg_replace_callback('/(\d+)$/', function ($matches) {
    //             return $matches[1] + 1;
    //         }, $max);
    //     }
    //     return "{$slug}-2";
    // }
}






