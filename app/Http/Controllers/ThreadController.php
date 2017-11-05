<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Thread;
use App\Filters\ThreadFilters;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ThreadController extends Controller
{

    public function __construct() {

        $this->middleware('auth')->except(['index','show']);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilters $filters)
    {

        $threads = $this->getThreads($channel,$filters);

        // dd($threads[0]);

        if (request()->wantsJson()) {

            return $threads;
        }

        return view('threads.index', compact('threads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([

            'title' => 'required|max:30|min:5|spamfree',
            'body' => 'required|min:5|max:2048|spamfree',
            'channel_id' => 'required|exists:channels,id'

        ]);

       $thread = Thread::create([
            'user_id' =>auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body'),
       ]);

       $thread->subscribe(auth()->id());

       return redirect($thread->path())
            ->with('flash', 'Your thread has been published');
    }

    /**
     * Display the specified resource.
     *
     * @param  $channelId
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread)
    {
        // Record that the user visited this thread as timestamp
        if (auth()->check()) auth()->user()->readed($thread);
        // dd(cache($key));
        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel, Thread $thread)
    {   

        // Apply Thread Policy
        $this->authorize('update', $thread);

        // if ($thread->user_id !== auth()->id()) {

        //     if (request()->wantsJson()) {
        //         return response(['status' => 'Permission Denied'], 403);
        //     }

        //     return back()->withErrors(['You don\'t have permission to delete this thread.']);
        // }

        // Delete Replies associated when a thread is deleted
        // Applied as static method on the Thread Model
        // Uncomment to apply on controller level
        // $thread->replies()->delete();

        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return redirect()->route('profile', auth()->user());
    }

    protected function getThreads(Channel $channel, ThreadFilters $filters) {

        $threads = Thread::latest()->filter($filters);

        if($channel->exists) {

            $threads->where('channel_id', $channel->id);
        }

        return $threads = $threads->get();

    }

}
