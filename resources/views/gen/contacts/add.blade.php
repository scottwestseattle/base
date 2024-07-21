@extends('layouts.app')
@section('title', __('proj.Add Contact'))
@section('menu-submenu')@component('gen.contacts.menu-submenu', ['prefix' => 'contacts'])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.Add Contact')}}</h1>
	<form method="POST" action="/contacts/create">

		<div class="form-group">
			<label for="name" class="control-label">@LANG('base.Name'):</label>
			<input type="text" name="name" class="form-control @error('model') is-invalid @enderror" required autofocus />
			@error('title')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>

		<div class="form-group">
			<label for="access" class="control-label">@LANG('ui.Access'):</label>
			<input type="text" name="access" class="form-control" />
		</div>

		<div class="form-group">
			<label for="lastUpdated" class="control-label">@LANG('ui.Updated'):</label>
			<input type="text" name="lastUpdated" class="form-control" />
		</div>

		<div class="form-group">
			<label for="address" class="control-label">@LANG('ui.Address'):</label>
			<input type="text" name="address" class="form-control" />
		</div>

		<div class="form-group">
			<label for="verifyMethod" class="control-label">@LANG('ui.Verify'):</label>
			<input type="text" name="verifyMethod" class="form-control" />
		</div>

		<div class="form-group">
			<label for="notes" class="control-label">{{trans_choice('ui.Note', 2)}}:</label>
			<textarea name="notes" class="form-control"></textarea>
		<div>

		<div class="form-group">
			<label for="numbers" class="control-label">{{trans_choice('ui.Number', 2)}}:</label>
			<input type="text" name="numbers" class="form-control" />
		</div>

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('ui.Add')</button>
		</div>

		{{ csrf_field() }}
	</form>
</div>
@endsection
