@extends('layouts.app')
@section('title', __('proj.Delete Article'))
@section('menu-submenu')@component('gen.articles.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('proj.Delete Article')}}</h1>
	<form method="POST" action="/articles/delete/{{ $record->id }}">

		<h4>{{$record->title}}</h4>

		<div class="submit-button mb-2">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

		<p>{{$record->description }}</p>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

	{{ csrf_field() }}
	</form>
</div>
@endsection
