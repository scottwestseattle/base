@extends('layouts.app')
@section('title', __('proj.Add Stat'))
@section('menu-submenu')@component('gen.stats.menu-submenu', ['prefix' => 'stats'])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.Add Stat')}}</h1>
	<form method="POST" action="/stats/create">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('base.Title'):</label>
			<input type="text" name="title" class="form-control @error('model') is-invalid @enderror" required autofocus />
			@error('title')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>

		<div class="form-group">
			<label for="description" class="control-label">@LANG('base.Description'):</label>
			<textarea name="description" class="form-control"></textarea>
		<div>

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('base.Add')</button>
		</div>

		{{ csrf_field() }}
	</form>
</div>
@endsection
