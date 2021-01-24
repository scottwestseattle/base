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
			<li>Blog</li>
			<li>Blog Entry</li>
			<li>Comments</li>
			<li>Contact Form</li>
			<li>Error Handling</li>
			<li>EU GDPR Compliance Notice</li>
			<li>Form Validation</li>
			<li>Forum</li>
			<li>Geo-enabled</li>
			<li>Icon</li>
			<li>Site Map</li>
			<li>User Access Levels</li>
			<li>Visitor Tracking</li>
			<li><strong><i>Domain-aware (multiple sites)</i></strong></li>
			<li><strong><i>Event Logging</i></strong></li>
			<li><strong><i>Https Certificate</i></strong></li>
			<li><strong><i>Hosting</strong></i></li>
			<li><strong><i>Soft Deletes</i></strong></li>
			<li><strong><i>Templates</i></strong></li>
			<li><strong><i>User Management</i></strong></li>
			<li><strong><i>Terms of Use</i></strong></li>
			<li><strong><i>Localization</i></strong></li>
			<li><strong><i>Privacy Policy</i></strong></li>
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
