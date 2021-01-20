@extends('layouts.app')
@section('title', __('ui.Edit') . ' ' . trans_choice('view.Comment', 1))
@section('menu-submenu')@component('comments.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('ui.Edit')}} {{trans_choice('base.Comment', 1)}}</h1>
	<form method="POST" id="form-edit" action="/comments/update/{{$record->id}}">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('base.Title'):</label>
			<input type="text" name="title" class="form-control" value="{{$record->title}}"></input>
		</div>

		<div class="form-group">
			<label for="description" class="control-label">@LANG('base.Description'):</label>
			<textarea name="description" class="form-control">{{$record->description}}</textarea>
		</div>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

