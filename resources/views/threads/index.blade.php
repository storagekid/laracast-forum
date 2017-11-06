@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h1 class="text-center">Forum Threads</h1>
            @include('threads._list')
        </div>
        <div class="col-md-8 col-md-offset-2 text-center">
            {{$threads->render()}}
        </div>
    </div>
</div>
@endsection