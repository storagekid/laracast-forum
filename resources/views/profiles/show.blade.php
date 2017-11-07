@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h2 class="text-center">My Profile</h2>
            <div class="panel panel-default">
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="col-xs-2">
                    @if ($userProfile->avatar_path)
                        <img src="{{asset('/storage/'.$userProfile->avatar())}}" width="50px" height="50px">
                    @else
                        @can ('update', $userProfile)
                            <form method="post" action="{{route('avatar', $userProfile)}}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <label class="btn btn-default btn-primary btn-sm">
                                    Select Image <input type="file" name="avatar" hidden>
                                </label>
                                <button type="submit" class="btn btn-default btn-info btn-sm">Add Avatar</button>
                            </form>
                        @endcan
                    @endif
                    </div>
                    <div class="col-xs-10">
                        <p>Name: <strong>{{$userProfile->name}}</strong></p>
                        <p>Email: <strong>{{$userProfile->email}}</strong></p>
                    </div>
                </div>
            </div>
            <h2 class="text-center">My Activities</h2>
            @forelse($activities as $date => $record)
                <h4 class="page-header">{{$date}}</h4>
                @foreach($record as $activity)
                    @if (view()->exists("profiles.activities.$activity->type"))
                        @include("profiles.activities.$activity->type")
                    @endif
                @endforeach
            @empty
                <h3 class="alert alert-info text-center">No activity yet.</h3>
            @endforelse
            {{-- {{$threads->links()}} --}}
        </div>
    </div>
</div>
@endsection