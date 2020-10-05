@extends('layouts.app')
@section('title', __('base.Delete Events'))
@section('menu-submenu')@component('events.menu-submenu')@endcomponent @endsection
@section('content')

<div class="">
	<h1>{{__('base.Delete Events')}} ({{count($records)}})</h1>

	<div class="text-lg">
		@if (count($records) > 0)
			<p><a href="/events/delete/">{{__('base.Delete All')}}</a></p>
			<p><a href="/events/delete/errors">{{__('base.Delete Errors')}}</a></p>
		@else
			<p>{{__('base.No events found')}}</p>
		@endif
		
		@if ($hasEmergencyEvents)
			<p><a href="/events/delete/emergency">{{__('base.Delete Emergency Events')}}</a></p>
		@endif
	</div>
</div>

@endsection