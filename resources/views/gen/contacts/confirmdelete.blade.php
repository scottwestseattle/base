@extends('layouts.app')
@section('title', __('proj.Delete Contact'))
@section('menu-submenu')@component('gen.contacts.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('proj.Delete Contact')}}</h1>
	<form method="POST" action="/contacts/delete/{{ $record->id }}">

		<h4>{{$record->title}}</h4>

		<p>{{$record->description }}</p>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

	{{ csrf_field() }}
	</form>
</div>
@endsection
