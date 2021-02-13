@extends('layouts.app')
@section('title', __('proj.Add Lesson'))
@section('menu-submenu')@component('gen.lessons.menu-submenu', ['prefix' => 'lessons'])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.Add Lesson')}}</h1>
	<form method="POST" action="/lessons/create">

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
