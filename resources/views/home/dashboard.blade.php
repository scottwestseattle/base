@extends('layouts.app')

@section('content')

	<h1>{{Auth::user()->name}} Dashboard</h1>
	<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
		<ul>
			<li>Exception Handling</li>
			<li>Password Update Email</li>
			<li>Password Reset Email</li>
			<li>Common View Data</li>
			<li>Form Field Validation</li>			
		</ul>
	</div>
	
	<h3>Operations</h3>
	<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
		<p><a href="/users/edit/{{Auth::user()->id}}">Update User</a></p>
		<p><a href="/passwords/reset">Update Password</a></p>
		<p><a href="/passwords/reset">Reset Password</a></p>
	</div>
	
@endsection
