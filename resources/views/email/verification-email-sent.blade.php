@extends('layouts.app')
@section('title', __('base.Verification Email Sent'))
@section('content')
<div>               
	<h1>Verification email has been sent</h1>
	
	<h2>{{__('base.Check for it at', ['email' => $user->email])}}</h2>
	
	<h4><i>{{ __('base.link will expire', ['mins' => getConstant("time.link_expiration_minutes")]) }}</i></h4>

	@component('components.check-email-steps') @endcomponent	
</div>

@endsection
