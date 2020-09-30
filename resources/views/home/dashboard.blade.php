@extends('layouts.app')

@section('content')

	<h1>{{Auth::user()->name}} Dashboard</h1>
	
	@if (!App\User::isConfirmed())
		<div class="text-center center">
		<h3>You email address has not been confirmed</h3>
		<p><a href="/email/send/{{Auth::id()}}">Click here to resend the confirmation email to {{Auth::user()->email}}</a></p>
		</div>
	@endif
	
	<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
		<ul>
			<li>Email Update Verification</li>
			<li>Email Verification Expiration</li>
			<li>Password Reset Email</li>
			<li>Form Field Validation</li>			
		</ul>
	</div>

@endsection
