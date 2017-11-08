@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h1 class="text-center">Forum Threads</h1>
            @include('threads._list')
        </div>
        @if($trending)
        <div class="col-xs-2">
        	<h3 class="text-center">Trending</h3>
        	<div class="panel panel-default">
        		<div class="panel-heading">
        		</div>
        		<div class="panel-body">
                    <ul class="list-group"></ul>
        			@foreach($trending as $tthread)
						<li class="list-group-item">
							<a href="{{$tthread->path}}">{{$tthread->title}}</a>
						</li>
        			@endforeach
        		</div>
        	</div>
        </div>
        @endif
        <div class="col-md-8 col-md-offset-2 text-center">
            {{$threads->render()}}
        </div>
    </div>
</div>
@endsection