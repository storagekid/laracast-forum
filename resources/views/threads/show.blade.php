@extends('layouts.app')

@section('header')
    <link href="{{ asset('css/vendor/jquery.atwho.css') }}" rel="stylesheet">
@endsection

@section('content')

<thread-view inline-template :incomedata="{{$thread->replies_count}}" v-cloak>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>{{ $thread->title }}</h3>
                        <a href="/profiles/{{ $thread->user->name }}">
                            <img src="{{$thread->user->avatar_path}}" width="50px" height="50px">
                            <span class="small">by {{$thread->user->name}}</span>
                        </a>
                    </div>
                    <div class="panel-body">
                         <p class="body">{{ $thread->body }}</p>
                    </div>
                </div>
                @if(count($errors))
                    @include('layouts.errors')
                @endif

                <replies 
                    @removed="repliesCount--" 
                    @added="repliesCount++">                    
                </replies>

            </div>
            {{-- Right SideBar --}}
            <div class="col-md-3 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-body text-center">
                         <h4>Publish</h4>
                         <p>{{$thread->created_at->diffForHumans()}}</p>
                         <h4>Threat Creator</h4>
                         <a href="{{ $thread->user->myThreads() }}"><span class="small">{{$thread->user->email}}</span></a>
                         <h4>Reply Counter</h4>
                         <p><span v-text="repliesCount"></span> {{str_plural('comment',$thread->replies_count)}}</p>
                         @if(auth()->check())
                         <p>
                            <subscribe-button :active="{{json_encode($thread->isSubscribeTo)}}"></subscribe-button>
                         </p>
                         @endif
                         @can('update',$thread)
                         <p>
                            <a href=""><button type="button" class="btn btn-sm btn-block btn-warning" >Edit</button></a>
                         </p>
                         <P>
                            <form action="{{ $thread->path() }}" method="post">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-sm btn-block btn-danger">Delete</button>
                             </form>
                         </P>
                         @endcan
                    </div>
                </div>
            </div>
        </div>           
    </div>

</thread-view>
@endsection