@extends('layouts.app')
@section('title', 'Email Verified')
@section('menu-submenu')@component('users.menu-submenu', ['record' => $user]) @endcomponent @endsection
@section('content')
<div>               
	<h1>Thank you for verifying your email address!</h1>

	<h2 class="mt-3">You can now use {{$user->email}} to recover your password.</h3>
</div>

@endsection
