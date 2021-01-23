@extends('layouts.app')
@section('title', __('ui.Edit') . ' ' . trans_choice('view.Site', 1))
@section('menu-submenu')@component('sites.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('ui.Edit')}} {{trans_choice('view.Site', 1)}}</h1>

	<form method="POST" id="form-edit" action="/sites/update/{{$record->id}}">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('ui.URL'):</label>
			<input type="text" name="title" class="form-control" value="{{$record->title}}"></input>
		</div>

		<div class="form-group">
			<label for="frontpage" class="control-label">@LANG('view.Frontpage'):</label>
			<input type="text" name="frontpage" class="form-control" placeholder="{{__('view.Enter frontpage view file name')}}" value="{{$record->frontpage}}" />
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

