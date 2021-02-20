@extends('layouts.app')
@section('title', __('proj.View Site'))
@section('menu-submenu')@component('sites.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.View Site')}}</h1>

	<h3 name="title">{{$record->title }}</h3>

    @if (!$record->isPublic())
        @component('components.button-release-status', ['record' => $record, 'views' => 'sites'])@endcomponent
    @endif

	<p class="mt-3">{{$record->description }}</p>
</div>
@endsection
