@extends('layouts.app')
@section('title', __('proj.View Contact'))
@section('menu-submenu')@component('gen.contacts.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.View Contact')}}</h1>

	<h3 name="title">{{$record->name}}</h3>

    @if (!empty($record->access))
    <div class="form-group">
        <b><label for="access" class="control-label">@LANG('ui.Access')</label></b>
    	<p class="ml-4">{{$record->access}}</p>
    </div>
    @endif

    @if (!empty($record->lastUpdated))
    <div class="form-group">
        <b><label for="lastUpdated" class="control-label">@LANG('ui.Updated')</label></b>
        <p class="ml-4">{{$record->lastUpdated}}</p>
    </div>
    @endif

    @if (!empty($record->address))
    <div class="form-group">
        <b><label for="address" class="control-label">@LANG('ui.Address')</label></b>
        <p class="ml-4">{{$record->address}}</p>
    </div>
    @endif

    @if (!empty($record->verifyMethod))
    <div class="form-group">
        <b><label for="verifyMethod" class="control-label">@LANG('ui.Verify')</label></b>
        <p class="ml-4">{{$record->verifyMethod}}</p>
    </div>
    @endif

    @if (!empty($record->notes))
    <div class="form-group">
        <b><label for="notes" class="control-label">{{trans_choice('ui.Note', 2)}}</label></b>
        <p class="ml-4">{!!str_replace("\r\n", "<br/>", $record->notes)!!}</p>
    <div>
    @endif

    @if (!empty($record->numbers))
    <div class="form-group">
        <b><label for="numbers" class="control-label">{{trans_choice('ui.Number', 2)}}</label></b>
        <p class="ml-4">{{format_number($record->numbers, true)}}</p>
    </div>
    @endif
</div>
@endsection
