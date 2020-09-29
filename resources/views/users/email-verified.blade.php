@extends('layouts.app')
@section('title', 'Email Verified')
@section('menu-submenu')@component('users.menu-submenu', ['record' => $user]) @endcomponent @endsection
@section('content')
<div>               
	<h1>Thank you for verifying your email address!</h1>

	<h2 class="mt-3">You now have full access to the web site and you can use {{$user->email}} for password recovery.</h3>
</div>

@endsection
