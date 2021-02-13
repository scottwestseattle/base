@extends('layouts.app')
@section('title', __('proj.View Course'))
@section('menu-submenu')@component('gen.courses.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.View Course')}}</h1>

	<h3 name="title">{{$record->title }}</h3>

    @component('components.button-release-status', ['record' => $record, 'views' => 'courses'])@endcomponent

	<p class="mt-3">{{$record->description }}</p>
</div>
@endsection
