@extends('layouts.app')
@section('title', __('ui.Delete Tag'))
@section('menu-submenu')@component('tags.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('ui.Delete Tag')}}</h1>
	<form method="POST" action="/tags/delete/{{ $record->id }}">

		<h4>{{$record->name}}</h4>

		<p>{{$record->getTypeFlagName() }}</p>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

	{{ csrf_field() }}
	</form>
</div>
@endsection
