@extends('layouts.app')
@section('title', __('ui.Add Entry'))
@section('menu-submenu')@component('entries.menu-submenu', ['prefix' => 'entries'])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('ui.Add Entry')}}</h1>
	<form method="POST" action="/entries/create">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('ui.Title'):</label>
			<input type="text" name="title" class="form-control @error('model') is-invalid @enderror" required autofocus />
			@error('title')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>

		<div class="form-group">
			<label for="description" class="control-label">@LANG('ui.Description'):</label>
			<textarea name="description" class="form-control"></textarea>
		<div>

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('ui.Add')</button>
		</div>

		{{ csrf_field() }}
	</form>
</div>
@endsection
