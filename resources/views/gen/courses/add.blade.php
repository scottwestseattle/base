@php
    $prefix = 'courses';
@endphp
@extends('layouts.app')
@section('title', __('proj.Add Course') )
@section('menu-submenu')@component('gen.courses.menu-submenu', ['prefix' => $prefix])@endcomponent @endsection
@section('content')

<h1>@LANG('proj.Add Course')</h1>

<form method="POST" action="/{{$prefix}}/create">

    <div class="form-group">
    @component('components.control-accent-chars-esp', ['flat' => true])@endcomponent
    <label for="title" class="control-label">@LANG('ui.Title'):</label>
    <input type="text" id="title" name="title" class="form-control" onfocus="setFocus($(this), '#accent-chars')" autofocus />
    </div>

    <div class="form-group">
        <label for="description" class="control-label">@LANG('ui.Description'):</label>
        <textarea id="description" name="description" class="form-control" onfocus="setFocus($(this), '#accent-chars')" ></textarea>
    <div>

    <div class="form-group mt-3">
        <div class="submit-button">
            <button type="submit" name="update" class="btn btn-primary">@LANG('ui.Add')</button>
        </div>
    </div>

    {{ csrf_field() }}

</form>

@endsection
