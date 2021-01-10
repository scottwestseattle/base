@extends('layouts.app')
@section('title', __('view.Add Test'))
@section('menu-submenu')@component('tests.menu-submenu', ['prefix' => 'tests'])@endcomponent @endsection
@section('content')
<div class="">
	<h1>@LANG('view.Add Test')</h1> 
	<form method="POST" action="/tests/create">
							
		<label for="title" class="control-label">@LANG('base.Title'):</label>
		<input type="text" name="title" class="form-control" />
		
		<div class="form-group">
			<label for="description" class="control-label">@LANG('base.Description'):</label>
			<textarea name="description" class="form-control"></textarea>
		<div>
		
		<div class="form-group">
			<div class="submit-button">
				<button type="submit" name="update" class="btn btn-primary">@LANG('base.Add')</button>
			</div>
		</div>
		
		{{ csrf_field() }}
	</form>
</div>
@endsection