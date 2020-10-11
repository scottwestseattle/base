@extends('layouts.app')
@section('title', 'Delete User')
@section('menu-submenu')@component('mvc.menu-submenu', []) @endcomponent @endsection
@section('content')
<div>
	<h1>Delete</h1>

	<form method="POST" action="/mvc/delete">

		<input name="views" type="hidden" value="{{$views}}" />

		<p>Model Template: {{$paths['modelOut']}}</p>
		<p>Controller Template: {{$paths['controllerOut']}}</p>
		<p>Views Templates: {{$paths['viewsOutPathWildcard']}}</p>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
