@extends('layouts.app')
@section('title', __('ui.View') . ' ' . trans_choice('view.Word', 1))
@section('menu-submenu')@component('words.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('ui.View')}} {{trans_choice('view.Word', 1)}}</h1>

	<h3 name="title">{{$record->title }}</h3>

    @component('components.button-release-status', ['record' => $record, 'views' => 'words'])@endcomponent

	<p class="mt-3">{{$record->description }}</p>
</div>
@endsection
