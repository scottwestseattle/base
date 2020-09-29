@extends('layouts.app')

@section('content')

	<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
		<h1>About</h1>
		
		<div class="ml-4 text-center text-lg text-gray-500 sm:text-right sm:ml-0">
			<p>Laravel v{{ Illuminate\Foundation\Application::VERSION }}</p>
			<p>{{ip_address()}}</p>

		@auth
			<p>{{Auth::user()->name}}</p>
			
			@if (is_admin())
				<p>Admin</p>
			@endif
		@endif
		
			<p>Info: {{\Config::get('constants.email_address.info')}}</p>
			<p>Support: {{\Config::get('constants.email_address.support')}}</p>		
		
		</div>
		
	</div>

@endsection