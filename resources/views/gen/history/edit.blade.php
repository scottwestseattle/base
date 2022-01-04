@extends('layouts.app')
@section('title', __('proj.Edit History'))
@section('menu-submenu')@component('gen.history.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('proj.Edit History')}}</h1>

	<form method="POST" id="form-edit" action="/history/update/{{$record->id}}">

		<div class="form-group">
			<label for="program_name" class="control-label">@LANG('proj.Program'):</label>
			<input type="text" name="program_name" class="form-control" value="{{$record->program_name}}"></input>
		</div>

		<div class="form-group">
			<label for="program_id" class="control-label">@LANG('proj.Program Id'):</label>
			<input type="text" name="program_id" class="form-control" value="{{$record->program_id}}"></input>
		</div>

		<div class="form-group">
			<label for="session_name" class="control-label">@LANG('proj.Session'):</label>
			<textarea name="session_name" class="form-control">{{$record->session_name}}</textarea>
		</div>

		<div class="form-group">
			<label for="session_id" class="control-label">@LANG('proj.Session Id'):</label>
			<textarea name="session_id" class="form-control">{{$record->session_id}}</textarea>
		</div>

		<div class="form-group">
			<label for="seconds" class="control-label">@LANG('proj.Seconds'):</label>
			<textarea name="seconds" class="form-control">{{$record->seconds}}</textarea>
		</div>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

