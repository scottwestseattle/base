@extends('layouts.app')
@section('title', __('view.View Visitor'))
@section('menu-submenu')@component('gen.visitors.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('view.View Visitor')}}</h1>
	<h3 name="title">{{$record->title }}</h3>
	<p>{{$record->description }}</p>
</div>
@endsection
