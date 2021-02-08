@extends('layouts.app')
@section('title', __('base.View Tag'))
@section('menu-submenu')@component('tags.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('base.View Tag')}}</h1>

	<h3 name="title">{{$record->name }}</h3>

	<p class="mt-3">{{$record->getTypeFlagName()}}</p>
</div>
@endsection
