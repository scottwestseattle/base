@extends('layouts.app')
@section('title', __('ui.View') . ' ' . trans_choice('view.Template', 1))
@section('menu-submenu')@component('gen.templates.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('ui.View')}} {{trans_choice('view.Template', 1)}}</h1>
	<h3 name="title">{{$record->title }}</h3>
	<p>{{$record->description }}</p>
</div>
@endsection
