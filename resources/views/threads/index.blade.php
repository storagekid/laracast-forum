@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h1 class="text-center">Forum Threads</h1>
            @forelse ($threads as $thread)
            <article>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="level">
                            <h4 class="title flex">
                                @if ($thread->hasUpdatesFor())
                                    <span class="label label-danger">New!</span>
                                @endif
                                <a href="{{$thread->path()}}">
                                        {{ $thread->title }}
                                </a>
                            </h4>
                            <strong>{{$thread->replies_count}} {{str_plural('Reply', $thread->replies_count)}}</strong>
                        </div>
                        <p>
                            <a href="{{route('profile', $thread->user)}}"><span class="small">by {{$thread->user->name}}</span></a>
                        </p>
                    </div>
                    <div class="panel-body">
                        {{ $thread->body }}
                    </div>
                </div>
            </article>
            @empty
            <h3 class="text-center">There are no threads for this channel.</h3>
            @endforelse
        </div>
    </div>
</div>
@endsection