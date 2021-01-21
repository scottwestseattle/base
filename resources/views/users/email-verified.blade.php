@extends('layouts.app')
@section('title', __('base.Email Verified'))
@section('content')
<div>               
	<h1>{{__('base.Thank you for verifying your email address!')}}</h1>
	<h2 class="mt-3">{{__('base.You now have full access to the web site.')}}</h2>
	<h2 class="mt-2">{{__('base.You can use :email for password recovery.', ['email' => $user->email])}}</h3>
</div>

@endsection
