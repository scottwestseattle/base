@extends('layouts.app')
@section('title', 'About')
@section('content')

<h1>About</h1>

<div class="">
	<p>PHP v{{phpversion()}}</p>
	<p>Laravel v{{ Illuminate\Foundation\Application::VERSION }}</p>
	<p>{{ipAddress()}}</p>
	<p>{{domainName()}}</p>
	<p><strong>Info</strong>: {{\Config::get('constants.email.info')}}</p>
	<p><strong>Support</strong>: {{\Config::get('constants.email.support')}}</p>		
	<p><a href="/sitemap">Site Map</a></p>		

@auth
	<hr />
	<h3>User</h3>
	<p><strong>Name</strong>: {{Auth::user()->name}}</p>
	<p><strong>Email</strong>: {{Auth::user()->email}}</p>
	
	@if (is_admin())
		<p><strong>User Type</strong>: Admin</p>
		<hr />
		<h3>System</h3>
		<p>{{env('APP_NAME')}}</p>
		<p><strong>Server Time</strong>: {{timestamp()}}</p>
		<p><strong>App Debug</strong>: {{env('APP_DEBUG')}}</p>
		<p><strong>Database</strong>: {{env('DB_DATABASE')}}</p>
		<p><strong>Mail Host</strong>: {{env('MAIL_HOST')}}</p>
		<p><strong>Sample Token</strong>: {{uniqueToken()}}</p>
	@endif
@endif

</div>
	
@endsection