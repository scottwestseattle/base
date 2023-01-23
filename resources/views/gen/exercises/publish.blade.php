@extends('layouts.app')
@section('title', __('proj.Publish Exercise'))
@section('menu-submenu')@component('gen.exercises.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')

<h1>{{__('proj.Publish Exercise')}}</h1>

<form method="POST" action="/exercises/publishupdate/{{$record->id}}">

    <h3 name="title" class="">{{$record->title}}</h3>

    <div class="form-group">
        @component('components.control-dropdown-menu', ['options' => $wip_flags, 'field_name' => 'wip_flag', 'prompt' => 'base.Work Status'])@endcomponent
    </div>

    <div class="form-group">
        @component('components.control-dropdown-menu', ['options' => $release_flags, 'field_name' => 'release_flag', 'prompt' => 'base.Release Status'])@endcomponent
    </div>

    <div class="submit-button">
        <button type="submit" class="btn btn-primary">@LANG('ui.Update')</button>
    </div>
{{ csrf_field() }}
</form>

@endsection
