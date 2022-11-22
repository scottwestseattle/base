@extends('layouts.app')
@section('title', __('proj.Delete Stat'))
@section('menu-submenu')@component('gen.stats.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('proj.Delete Stat')}}</h1>
	<form method="POST" action="/stats/delete/{{ $record->id }}">

		<h4>{{$record->title}}</h4>

		<p>{{$record->description }}</p>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

	{{ csrf_field() }}
	</form>
</div>
@endsection
