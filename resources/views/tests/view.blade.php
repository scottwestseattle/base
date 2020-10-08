@extends('layouts.app')
@section('title', __('view.View Test'))
@section('menu-submenu')@component('tests.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('view.View Test')}}</h1>
	<h3 name="title">{{$record->title }}</h3>
	<p>{{$record->description }}</p>
</div>
@endsection
