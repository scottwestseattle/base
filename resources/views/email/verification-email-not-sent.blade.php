@extends('layouts.app')
@section('title', 'Verification Email Not Sent')
@section('content')
<div>               
	<h1 class="mb-4">Unable to send verification email</h1>

	<h2 class="mb-4">Our support team has been notified</h2>
	
	<h5 class="mb-4">If this is an emergency, please contact us at:</h5>
	
	<p class="text-lg">{{\Config::get('constants.email.support')}}</p>
</div>

@endsection
