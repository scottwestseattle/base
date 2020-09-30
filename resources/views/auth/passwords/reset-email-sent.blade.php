@extends('layouts.app')
@section('title', 'Password Reset Email')
@section('menu-submenu')@component('users.menu-submenu', ['record' => $user]) @endcomponent @endsection
@section('content')
<div>               
	<h3>If the email address you entered was correct then an email will be sent</h3>
	
	<h3>{{$email}}</h3>
	
	<h3>Check your email and click on the link to reset your password.</h3>
	
	<h5 class="mt-5">Note: If you do not receive the email in few minutes:</h5>
	<ul class="text-lg">
		<li>check your spam folder</li>
		<li>verify that the email address you entered was correct</li>
		<li>if you can't resolve the issue, please contact {{Config::get('constants.email.support'}}</li>
	</ul>
	
</div>

@endsection
