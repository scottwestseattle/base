@extends('layouts.app')
@section('title', 'Site Map')
@section('content')

<h1>Site Map</h1>

<div class="sm:px-4 lg:px-8">

	<p><a href="/about">About</a></p>
	<p><a href="/password/request-reset">Forgot Password</a></p>
	<p><a href="/">Front Page</a></p>
	<p><a href="/login">Log-in</a></p>
	<p><a href="/users/register">Register</a></p>
	
	@auth
	<hr/>
	<h4>User</h4>
	<p><a href="/dashboard">Dashboard</a></p>		
	<p><a href="/users/edit/{{Auth::id()}}">Edit Profile</a></p>
	<p><a href="/users/view/{{Auth::id()}}">Profile</a></p>
	<p><a href="/password/edit/{{Auth::id()}}">Update Password</a></p>
	<p><a href="/logout">Log-out</a></p>
	
	@if (is_admin())
		<hr/>
		<h4>Admin</h4>
		<p><a href="/events/confirmdelete">Delete Events</a></p>
		<p><a href="/events">Events</a></p>
		<p><a href="/translations">Translations</a></p>		
		<p><a href="/users">Users</a></p>		
	@endif
	
	@endauth
</div>

@endsection