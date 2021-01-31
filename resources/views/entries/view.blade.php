@extends('layouts.app')
@section('title', $record->title)
@section('menu-submenu')@component('entries.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="">
	<h3 name="title">{{$record->title }}</h3>

    @component('components.button-release-status', ['record' => $record, 'views' => 'entries'])@endcomponent

	<p class="mt-3">{{$record->description }}</p>
</div>
@endsection
