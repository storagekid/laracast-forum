@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create a new Thread</div>

                <div class="panel-body">
                    <form action="/threads" method="POST" role="form">
                        {{csrf_field()}}
                        @if(count($errors))
                            @include('layouts.errors')
                        @endif
                        <div class="form-group">
                            <label for="channel_id">Choose a Channel</label>
                            <select name="channel_id" id="channel_id" class="form-control" required>
                                <option value="">Choose One...</option>
                                @foreach($channels as $channel)
                                <option value="{{$channel->id}}" {{  old('channel_id')==$channel->id ? 'selected' : ''}}>{{$channel->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="title">Thread Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Name the thread" value="{{old('title')}}" required>
                        </div>
                        <div class="form-group">
                            <label for="body">Thread Body</label>
                            <textarea class="form-control" id="body" name="body" placeholder="Write something interesting..." required>{{old('body')}}</textarea>

                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Add Thread</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection