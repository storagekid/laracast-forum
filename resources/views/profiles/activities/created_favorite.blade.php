@component('profiles.activities.activity')
	@slot('heading')
		
		{{ $userProfile->name }} liked the reply <a href="{{ $activity->subject->favorite->path() }}">{{$activity->subject->favorite->body}}</a>.

	@endslot

	@slot('body')
		
		Date: {{ $activity->created_at->diffForHumans() }}

	@endslot
@endcomponent