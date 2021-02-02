@extends('layouts.app')
@section('title', __('proj.View Definition'))
@section('menu-submenu')@component('gen.definitions.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.View Definition')}}</h1>

	<h3 name="title">{{$record->title }}</h3>

	<p class="mt-3">{{$record->definition }}</p>
	<p class="mt-3">{{$record->translation_en }}</p>
	<p class="mt-3">{{$record->examples }}</p>
</div>
@endsection
