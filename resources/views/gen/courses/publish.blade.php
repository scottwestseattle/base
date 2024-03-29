@php
    $prefix = 'courses';
@endphp
@extends('layouts.app')
@section('title', __('proj.Publish Course') )
@section('menu-submenu')@component('gen.courses.menu-submenu', ['prefix' => $prefix])@endcomponent @endsection
@section('content')

<h1>{{__('proj.Publish Course')}}</h1>

<form method="POST" action="/{{$prefix}}/publishupdate/{{ $record->id }}">

    <h3 name="title" class="">{{$record->title }}</h3>

    <div class="form-group">
        @component('components.control-dropdown-menu', ['options' => $wip_flags, 'field_name' => 'wip_flag', 'prompt' => 'base.Work Status', 'selected_option' => $record->wip_flag])@endcomponent
    </div>

    <div class="form-group">
        @component('components.control-dropdown-menu', ['options' => $release_flags, 'field_name' => 'release_flag', 'prompt' => 'base.Release Status', 'selected_option' => $record->release_flag])@endcomponent
    </div>

    <div class="submit-button">
        <button type="submit" class="btn btn-primary">{{__('ui.Update')}}</button>
    </div>
{{ csrf_field() }}
</form>

@endsection
