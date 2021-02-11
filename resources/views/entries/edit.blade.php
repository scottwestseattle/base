@extends('layouts.app')
@section('title', __('base.Edit Entry'))
@section('menu-submenu')@component('entries.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('base.Edit Entry')}}</h1>

	<form method="POST" id="form-edit" action="/entries/update/{{$record->id}}">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('base.Title'):</label>
			<input type="text" name="title" class="form-control" value="{{$record->title}}"></input>
		</div>

		<div class="form-group">
			<label for="description" class="control-label">@LANG('ui.Description'):</label>
			<textarea name="description" class="form-control">{{$record->description}}</textarea>
		</div>

		@component('components.control-entry-types', [
		    'current_type' => $record->type_flag,
		    'entryTypes' => App\Entry::getEntryTypes()
		    ])@endcomponent

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

