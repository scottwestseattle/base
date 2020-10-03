@extends('layouts.app')
@section('title', __('base.Password Reset Email Sent'))
@section('content')
<div>
	<div class="">
		<h3>{{__('base.If the email address you entered was correct, an email will be sent to:')}}</h3>
		<h4 class="m-3">{{$email}}</h4>
		<h5 class="mb-5">{{__('base.Check your email and click on the link to reset your password.') }}</h5>
		<h5><i>{{ __('base.link will expire', ['mins' => getConstant("time.link_expiration_minutes")]) }}</i></h5>
		@component('components.check-email-steps') @endcomponent	
	</div>
	
</div>

@endsection
