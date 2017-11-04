@component('profiles.activities.activity')
	@slot('heading')
		
		{{ $userProfile->name }} reply {{$activity->subject->body}}.

	@endslot

	@slot('body')
		
		Date: {{ $activity->created_at->diffForHumans() }}

	@endslot
@endcomponent