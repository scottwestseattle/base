@extends('layouts.app')
@section('title', __('proj.View History'))
@section('menu-submenu')@component('gen.history.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.View History')}}</h1>

	<h3 name="title">{{$record->title }}</h3>

    @component('components.button-release-status', ['record' => $record, 'views' => 'histories'])@endcomponent

	<p class="mt-3">{{$record->description }}</p>
</div>
@endsection
