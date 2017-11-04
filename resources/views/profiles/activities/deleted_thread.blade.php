@component('profiles.activities.activity')
	@slot('heading')
		{{ $userProfile->name }} deleted the thread {{$activity->subject_id}}.

	@endslot

	@slot('body')
		
		Date: {{ $activity->created_at->diffForHumans() }}

	@endslot
@endcomponent