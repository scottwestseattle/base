@extends('layouts.app')
@section('title', __('proj.Delete History'))
@section('menu-submenu')@component('gen.history.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
@php
@endphp
<div class="container page-normal">

	<h1>{{__('proj.Delete History')}}</h1>
	<form method="POST" action="/history/delete/{{ $record->id }}">

		<h4>{{$record->getProgramName()}}</h4>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

	{{ csrf_field() }}
	</form>
</div>
@endsection
