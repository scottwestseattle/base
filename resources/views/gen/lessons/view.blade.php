@extends('layouts.app')
@section('title', __('proj.View Lesson'))
@section('menu-submenu')@component('gen.lessons.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.View Lesson')}}</h1>

	<h3 name="title">{{$record->title }}</h3>

	<p class="mt-3">{{$record->description }}</p>

	<div class="mt-3">{!!$record->text!!}</div>

</div>
@endsection
