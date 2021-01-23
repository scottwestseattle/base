@extends('layouts.app')
@section('title', __('ui.Publish') . ' ' . trans_choice('view.Word', 1))
@section('menu-submenu')@component('words.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('ui.Publish')}} {{trans_choice('view.Word', 1)}}</h1>

	<form method="POST" action="/words/publishupdate/{{$record->id}}">

		<h3 name="title" class="">{{$record->title}}</h3>

		<div class="form-group">
			<label for="wip_flag" class="control-label">@LANG('base.Work Status'):</label>
			<select name="wip_flag" class="form-control">
				@foreach ($wip_flags as $key => $value)
					<option value="{{$key}}" {{ $key == $record->wip_flag ? 'selected' : ''}}>{{$value}}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group">
			<label for="release_flag" class="control-label">@LANG('base.Release Status'):</label>
			<select name="release_flag" class="form-control">
				@foreach ($release_flags as $key => $value)
					<option value="{{$key}}" {{ $key == $record->release_flag ? 'selected' : ''}}>{{$value}}</option>
				@endforeach
			</select>
		</div>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Update')</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
