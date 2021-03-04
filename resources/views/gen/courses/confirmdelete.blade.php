@php
    $prefix = 'courses';
@endphp
@extends('layouts.app')
@section('title', __('proj.Delete Course') )
@section('menu-submenu')@component('gen.courses.menu-submenu', ['prefix' => $prefix])@endcomponent @endsection
@section('content')

<h1>@LANG('proj.Delete Course')</h1>

<form method="POST" action="/{{$prefix}}/delete/{{ $record->id }}">

@if (isset($children) && count($children) > 0)

    <h3 name="title" class="">{{$record->title}} ({{count($children)}} {{strtolower(trans_choice('proj.Lesson', 2))}})</h3>
    <p>{{__('proj.A course with lessons cannot be deleted.')}}</p>
    <p>{{__('proj.Please move or remove the lessons first.')}}</p>

    @component('gen.lessons.comp-lesson-list', ['records' => $children])@endcomponent

@else

    <h3 name="title" class="">{{$record->title}}</h3>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
    </div>

    <p>{{$record->permalink }}</p>

    <p>{{$record->description }}</p>

    <div class="submit-button">
        <button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
    </div>

@endif

{{ csrf_field() }}
</form>

@endsection
