@forelse ($threads as $thread)
<article>
    <div class="panel panel-default col-xs-6">
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
                <a href="{{route('profile', $thread->user)}}">
                    <img src="{{asset('/storage/'.$thread->user->avatar())}}" width="50px" height="50px">
                    <span class="small">by {{$thread->user->name}}</span>
                </a>
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