@extends('layouts.app')
@section('title', __('base.Dashboard'))
@section('content')

	<h1>{{__('base.Dashboard')}}</h1>
	
	@if (!App\User::isConfirmed())
		<div class="text-center center">
		<h3>You email address has not been confirmed</h3>
		<p><a href="/email/send/{{Auth::id()}}">Click here to resend the confirmation email to {{Auth::user()->email}}</a></p>
		</div>
	@endif
	
	<div class="">
		<ul>
			<li>Form Field Validation</li>
			<li>What else?</li>
		</ul>
	</div>
	
	@if (isAdmin())
		<hr />
		<h1>{{trans_choice('base.Event', 2)}} ({{count($events['records'])}})
		<h4>{{__('base.Emergency Events') }} ({{$events['emergency']}})</h4>
		<h4>{{__('base.Error Events')}} ({{$events['errors']}})</h4>
		<p><a href="/events">{{__('base.Go to Events')}}</a>
		
		<hr />
		<h1>{{trans_choice('base.User', 2)}} ({{$users}})</h1>
		<p><a href="/users">{{__('base.Go to Users')}}</a>
	@endif

@endsection
