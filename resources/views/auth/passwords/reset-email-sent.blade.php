@extends('layouts.app')
@section('title', 'Password Reset Email Sent')
@section('content')
<div>
	<div class="">
		<h3>If the email address you entered was correct, an email will be sent to:</h3>
		<h4 class="m-3">{{$email}}</h4>
		<h5>Check your email and click on the link to reset your password.</h5>
		<div class="text-left">
			<h5 class="mt-3"><strong>Note</strong>: If you don't receive the email in few minutes:</h5>
			<ul class="text-lg">
				<li>check your spam folder</li>
				<li>verify that the email address you entered was correct</li>
				<li>if you can't resolve the issue, please contact {{Config::get('constants.email.support')}}</li>
			</ul>
		</div>
	</div>
	
</div>

@endsection
