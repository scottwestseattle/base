@extends('layouts.app')
@section('title', 'Site Map')
@section('content')

<h1>Site Map</h1>

<div class="sm:px-4 lg:px-8">
	<p><a href="/about">About</a></p>
	<p><a href="/">Front Page</a></p>
	<p><a href="/login">Log-in</a></p>
	<p><a href="/users/register">Register</a></p>
	<p><a href="/password/request-password-reset">Forgot Password</a></p>	
</div>

@endsection