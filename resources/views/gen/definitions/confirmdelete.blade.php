@extends('layouts.app')
@section('title', __('ui.Delete') . ' ' . trans_choice('view.Definition', 1))
@section('menu-submenu')@component('gen.definitions.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('ui.Delete')}} {{trans_choice('view.Definition', 1)}}</h1>
	<form method="POST" action="/definitions/delete/{{ $record->id }}">

		<h4>{{$record->title}}</h4>

		<p>{{$record->description }}</p>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

	{{ csrf_field() }}
	</form>
</div>
@endsection
