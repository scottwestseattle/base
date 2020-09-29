@extends('layouts.app')
@section('title', 'Verification Email Sent')
@section('menu-submenu')@component('users.menu-submenu', ['record' => $user]) @endcomponent @endsection
@section('content')
<div>               
	<h1>Verification email has been sent</h1>
	
	<h2>Check for it at {{$user->email}}</h2>
	
	<h5 class="mt-5">Note: If you do not receive the email in few minutes:</h5>
	<ul class="text-lg">
		<li>check your spam folder</li>
		<li>verify that your email address is correct</li>
		<li>if you can't resolve the issue, please contact support@domain.com</li>
	</ul>
	
</div>

@endsection
