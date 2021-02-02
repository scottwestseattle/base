@extends('layouts.app')
@section('title', __('ui.Add') . ' ' . trans_choice('view.Definition', 1))
@section('menu-submenu')@component('gen.definitions.menu-submenu', ['prefix' => 'definitions'])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('ui.Add')}} {{trans_choice('view.Definition', 1)}}</h1>
	<form method="POST" action="/definitions/create">

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
			<label for="definition" class="control-label">{{trans_choice('proj.Definition', 1)}}:</label>
			<textarea name="definition" class="form-control"></textarea>
		</div>

		<div class="form-group">
			<label for="translation_en" class="control-label">@LANG('proj.Translation'):</label>
			<textarea name="translation_en" class="form-control"></textarea>
		</div>

		<div class="form-group">
			<label for="examples" class="control-label">{{trans_choice('proj.Example', 2)}}:</label>
			<textarea name="examples" class="form-control"></textarea>
		</div>

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('base.Add')</button>
		</div>

		{{ csrf_field() }}
	</form>
</div>
@endsection
