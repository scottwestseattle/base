@extends('layouts.app')
@section('title', __('ui.Delete') . ' ' . __('base.MVC'))
@section('menu-submenu')@component('mvc.menu-submenu', []) @endcomponent @endsection
@section('content')
<div>
	<h1>@LANG('ui.Delete') @LANG('base.MVC')</h1>

	<form method="POST" action="/mvc/delete">

		<input name="views" type="hidden" value="{{$views}}" />
		<input name="topLevel" type="hidden" value="{{$topLevel}}" />

		<p>Model Template: {{$paths['modelOut']}}</p>
		<p>MySQL Table Schema: {{$paths['mysqlSchemaOut']}}</p>
		<p>Controller Template: {{$paths['controllerOut']}}</p>
		<p>Views Templates: {{$paths['viewsOutPathWildcard']}}</p>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
