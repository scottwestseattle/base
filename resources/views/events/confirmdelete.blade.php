@extends('layouts.app')
@section('title', 'Delete Events')
@section('menu-submenu')@component('events.menu-submenu')@endcomponent @endsection
@section('content')

<div class="">
	<h1>Delete Events ({{count($records)}})</h1>

	<div class="text-lg">
		@if (count($records) > 0)
			<p><a href="/events/delete/">Delete All</a></p>
		@else
			<p>No events found</p>
		@endif
		
		@if ($hasEmergencyEvents)
			<p><a href="/events/delete/emergency">Delete Emergency Event</a></p>
		@endif
		{{--<p><a href="/events/delete/errors">Delete Errors</a></p>--}}
	</div>
</div>

@endsection