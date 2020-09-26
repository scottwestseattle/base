@extends('layouts.app')

@section('content')

@component('users.menu-submenu', ['record' => $record])@endcomponent

<div class="container">
	<h1>Delete</h1>

	<form method="POST" action="/users/delete/{{$record->id}}">

		<div class="form-group">
			<h2>{{$record->name}}</h2>
			<h3>{{$record->email}}</h3>	
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
