@extends('layouts.app')
@section('title', __('ui.Edit Tag'))
@section('menu-submenu')@component('tags.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('ui.Edit Tag')}}</h1>

	<form method="POST" id="form-edit" action="/tags/update/{{$record->id}}">

		<div class="form-group">
			<label for="name" class="control-label">@LANG('ui.Name'):</label>
			<input type="text" name="name" class="form-control" value="{{$record->name}}"></input>
		</div>

		<div class="form-group">
			<label for="type_flag" class="control-label">@LANG('ui.Type'):</label>
			<input type="text" name="type_flag" class="form-control" value="{{$record->type_flag}}"></input>
			<p>{{$record->getTypeFlagName()}}</p>
		</div>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

