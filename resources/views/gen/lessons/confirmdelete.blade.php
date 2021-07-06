@php
    $prefix = 'lessons';
    $isAdmin = isAdmin();
@endphp
@extends('layouts.app')
@section('title', __('proj.Delete Lesson'))
@section('menu-submenu')@component('gen.lessons.menu-submenu', ['prefix' => $prefix])@endcomponent @endsection
@section('content')

<div class="container page-normal">

	<h1>@LANG('proj.Delete Lesson')</h1>

	<h3 name="title" class="">{{$record->course->title}}: {{$record->title }}</h3>

	<form method="POST" action="/{{$prefix}}/delete/{{ $record->id }}">

		<div class="form-group">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

		<p>{{$record->permalink }}</p>

		<p>{{$record->description }}</p>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

	{{ csrf_field() }}
	</form>
</div>
@endsection
