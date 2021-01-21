@extends('layouts.app')
@section('title', 'Delete User')
@section('menu-submenu')@component('users.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
<div>
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
