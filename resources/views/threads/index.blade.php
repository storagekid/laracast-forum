@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h1 class="text-center">Forum Threads</h1>
            @include('threads._list')
        </div>
        <div class="col-xs-2">
        	<h3 class="text-center">Trending</h3>
        	<div class="panel panel-default">
        		<div class="panel-heading">
        		</div>
        		<div class="panel-body">
        			@foreach($trending as $tthread)
						<h6>
							<a href="{{$tthread->path}}">{{$tthread->title}}</a>
						</h6>
        			@endforeach
        		</div>
        	</div>
        </div>
        <div class="col-md-8 col-md-offset-2 text-center">
            {{$threads->render()}}
        </div>
    </div>
</div>
@endsection