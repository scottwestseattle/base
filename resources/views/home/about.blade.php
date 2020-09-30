@extends('layouts.app')
@section('title', 'About')
@section('content')

<h1>About</h1>

<div class="">
	<p>Laravel v{{ Illuminate\Foundation\Application::VERSION }}</p>
	<p>{{ip_address()}}</p>

@auth
	<p>{{Auth::user()->name}}</p>
	
	@if (is_admin())
		<p>Admin</p>
	@endif
@endif

	<p>Info: {{\Config::get('constants.email.info')}}</p>
	<p>Support: {{\Config::get('constants.email.support')}}</p>		
	<p><a href="/sitemap">Site Map</a></p>		

</div>
	
@endsection