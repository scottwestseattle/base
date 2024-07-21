@extends('layouts.app')
@section('title', __('proj.Edit Contact'))
@section('menu-submenu')@component('gen.contacts.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('proj.Edit Contact')}}</h1>

	<form method="POST" id="form-edit" action="/contacts/update/{{$record->id}}">

		<div class="form-group">
			<label for="name" class="control-label">@LANG('ui.Name'):</label>
			<input type="text" name="name" class="form-control" value="{{$record->name}}"></input>
		</div>

		<div class="form-group">
			<label for="access" class="control-label">@LANG('ui.Access'):</label>
			<input type="text" name="access" class="form-control" value="{{$record->access}}"></input>
		</div>

		<div class="form-group">
			<label for="lastUpdated" class="control-label">@LANG('ui.Updated'):</label>
			<input type="text" name="lastUpdated" class="form-control" value="{{$record->lastUpdated}}"></input>
		</div>

		<div class="form-group">
			<label for="verifyMethod" class="control-label">@LANG('ui.Verify'):</label>
			<input type="text" name="verifyMethod" class="form-control" value="{{$record->verifyMethod}}"></input>
		</div>

		<div class="form-group">
			<label for="address" class="control-label">@LANG('ui.Address'):</label>
			<input type="text" name="address" class="form-control" value="{{$record->address}}"></input>
		</div>

		<div class="form-group">
			<label for="notes" class="control-label">{{trans_choice('ui.Note', 2)}}:</label>
			<textarea name="notes" class="form-control">{{$record->notes}}</textarea>
		</div>

		<div class="form-group">
			<label for="numbers" class="control-label">{{trans_choice('ui.Number', 2)}}:</label>
			<input type="text" name="numbers" class="form-control" value="{{format_number($record->numbers, true)}}"></input>
		</div>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

