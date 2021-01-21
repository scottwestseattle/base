@extends('layouts.app')
@section('title', __('ui.Add') . ' ' . trans_choice('view.Template', 1))
@section('menu-submenu')@component('gen.templates.menu-submenu', ['prefix' => 'templates'])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('ui.Add')}} {{trans_choice('view.Template', 1)}}</h1>
	<form method="POST" action="/templates/create">

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
