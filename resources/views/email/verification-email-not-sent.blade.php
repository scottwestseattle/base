@extends('layouts.app')
@section('title', 'Email Verified')
@section('menu-submenu')@component('users.menu-submenu', ['record' => $user]) @endcomponent @endsection
@section('content')
<div>               
	<h1>Unable to send verification email</h1>

	<h2 class="mt-4">Our support team has been notified</h2>
	
	<h5 class="mt-4">If this is an emergency, please contact us at {{\Config::get('constants.email_address.support')}}</h5>
	
</div>

@endsection
