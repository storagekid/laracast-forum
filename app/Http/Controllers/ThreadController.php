<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Thread;
use App\Trending;
use App\Filters\ThreadFilters;
use App\Rules\Recaptcha;
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
    public function index(Channel $channel, ThreadFilters $filters, Trending $trending)
    {
        $threads = $this->getThreads($channel,$filters);
        // $trending = array_map('json_decode', Redis::zrevrange('trending_threads',0,4));
        if (request()->wantsJson()) {
            return $threads;
        }
        $trending = Thread::orderBy('visits_count','desc')->take(5)->get();
        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending
        ]);
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
    public function store(Request $request, Recaptcha $recaptcha)
    {
        // if (! auth()->user()->confirmed) {
        //     return redirect('/threads')
        //         ->with('flash', 'You must first confirm your email address.');
        // }

        request()->validate([
            'title' => 'required|max:255|min:5|spamfree',
            'body' => 'required|min:5|max:2048|spamfree',
            'channel_id' => 'required|exists:channels,id',
            'g-recaptcha-response' => ['required', $recaptcha],
        ]);
        // dd(session()->all());
       $thread = Thread::create([
            'user_id' =>auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body')
            // 'slug' => request('title'), // Using Custom Mutator behind the scenes to prevent duplication
       ]);

       $thread->subscribe(auth()->id());

       if (request()->wantsJson()) {
            return response($thread, 201);
        }

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

        // Trending::set('trending_threads', 1, [
        //     'title' => $thread->title,
        //     'path' => $thread->path(),
        // ]);
        // Record visits with Redis
        // $thread->visits()->record();
        // Record visits in DB
        $thread->increment('visits_count');
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
    public function update(Channel $channel, Thread $thread)
    {
        // Authorization
        $this->authorize('update', $thread);
        // Validation
        // Update
        $thread->update(request()->validate([
            'title' => 'required|max:255|min:5|spamfree',
            'body' => 'required|min:5|max:2048|spamfree',
            // 'channel_id' => 'required|exists:channels,id',
            // 'g-recaptcha-response' => ['required', $recaptcha],
        ]));

        return $thread;
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
        return $threads = $threads->paginate(4);
    }
}
