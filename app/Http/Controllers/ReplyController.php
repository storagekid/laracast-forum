<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;
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

    public function store($channelId, Thread $thread) {

        try {
            $this->validateReply();

            $reply = $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ]);
        } catch(\Exception $e) {
            return response('Sorry your reply could not be saved at this time.', 422);
        }

        if (request()->expectsJson()) {
            return $reply->load('owner');
        }
    	return back()->with('flash', 'Your reply has been published');
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

        try {
            $this->validateReply();

            $reply->update([

                'body' => request('body'),
            ]);
        } catch(\Exception $e) {
            return response('Sorry your reply could not be updated at this time.', 422);
        }
    }

    public function validateReply() {
        request()->validate(['body' => 'required|min:5|spamfree',]);
    }

}
