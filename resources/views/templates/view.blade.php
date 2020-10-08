@extends('layouts.app')
@section('title', __('view.View Template'))
@section('menu-submenu')@component('templates.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('view.View Template')}}</h1>
	<h3 name="title">{{$record->title }}</h3>
	<p>{{$record->description }}</p>
</div>
@endsection
