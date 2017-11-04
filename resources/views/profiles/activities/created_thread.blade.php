@component('profiles.activities.activity')
	@slot('heading')
		
		{{ $userProfile->name }} created the thread {{$activity->subject->title}}.

	@endslot

	@slot('body')
		
		Date: {{ $activity->created_at->diffForHumans() }}

	@endslot
@endcomponent