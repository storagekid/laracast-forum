<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreatePostForm;
use App\Http\Requests\UpdatePostForm;
use App\Thread;
use App\User;
use App\Favorite;
use App\Reply;

class ReplyController extends Controller
{

	public function __construct() {
		$this->middleware('auth')->except('index');
	}

    public function index($channelId, Thread $thread) {
        return $thread->replies()->latest()->paginate(10);
    }

    public function store($channelId, Thread $thread, CreatePostForm $form) {
        // $reply = $form->persist($thread);
        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');
    }

    public function destroy(Reply $reply) {  

        $this->authorize('update',$reply);
        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status'=>'Reply deleted']);
        }

        return back()->with('flash', 'Reply successfully deleted');
    }

    public function update(Reply $reply) {
        $this->authorize('update',$reply);
        request()->validate(['body'=> 'required|spamfree']);
        $reply->update([
            'body' => request('body'),
        ]);
    }
}
