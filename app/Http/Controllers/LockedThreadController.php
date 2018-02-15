<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;

class LockedThreadController extends Controller
{
    public function store(Thread $thread) {
	    $thread->update(['locked' => true]);
	    return response('Thread Locked', 200);
    }
    public function destroy(Thread $thread) {
    	$thread->update(['locked' => false]);
    	return response('Thread Unlocked', 200);
    }
}
