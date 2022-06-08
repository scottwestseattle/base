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
			<input type="number" name="program_id" class="form-control" value="{{$record->program_id}}"></input>
		</div>

		<div class="form-group">
			<label for="type_flag" class="control-label">@LANG('proj.Program Type'):</label>
			<input type="number" name="type_flag" class="form-control" value="{{$record->type_flag}}"></input>
		</div>

		<div class="form-group">
			<label for="subtype_flag" class="control-label">@LANG('proj.Program Subtype'):</label>
			<input type="number" name="subtype_flag" class="form-control" value="{{$record->subtype_flag}}"></input>
		</div>

		<div class="form-group">
			<label for="route" class="control-label">@LANG('proj.Route'):</label>
			<input type="text" name="route" class="form-control" value="{{$record->route}}" />
		</div>

		<div class="form-group">
			<label for="session_name" class="control-label">@LANG('proj.Session'):</label>
			<input type="text" name="session_name" class="form-control" value="{{$record->session_name}}" />
		</div>

		<div class="form-group">
			<label for="session_id" class="control-label">@LANG('proj.Session Id'):</label>
			<input type="number" name="session_id" class="form-control" value="{{$record->session_id}}" />
		</div>

		<div class="form-group">
			<label for="count" class="control-label">@LANG('proj.Count'):</label>
			<input type="number" name="cont" class="form-control" value="{{$record->count}}" />
		</div>
		<div class="form-group">
			<label for="seconds" class="control-label">@LANG('proj.Seconds'):</label>
			<input type="number" name="seconds" class="form-control" value="{{$record->seconds}}" />
		</div>

		<div class="form-group">
			<label for="score" class="control-label">@LANG('proj.Score'):</label>
			<input type="number" name="score" class="form-control" value="{{$record->score}}" />
		</div>

		<div class="form-group">
			<label for="extra" class="control-label">@LANG('proj.Extra'):</label>
			<input type="number" name="extra" class="form-control" value="{{$record->extra}}" />
		</div>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

