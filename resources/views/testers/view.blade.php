@extends('layouts.app')
@section('title', __('view.View Tester'))
@section('menu-submenu')@component('testers.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('view.View Tester')}}</h1>
	<h3 name="title">{{$record->title }}</h3>
	<p>{{$record->description }}</p>
</div>
@endsection
