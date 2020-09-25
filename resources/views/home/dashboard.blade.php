@extends('layouts.app')

@section('content')

	<h1>{{Auth::user()->name}} Dashboard</h1>
	<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
		<div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
		</div>
		<ul>
			<li>Exception Handling</li>
			<li>Event Log</li>
			<li>Password Reset Email</li>
			<li>No Session Timeout</li>
			<li>Common View Data</li>
			<li>Form Field Validation</li>			
		</ul>
	</div>

@endsection