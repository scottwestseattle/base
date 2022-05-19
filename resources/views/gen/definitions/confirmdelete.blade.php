@extends('layouts.app')
@section('title', __('proj.Delete Definition'))
@section('menu-submenu')@component('gen.definitions.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
<div class="container page-normal">

    @if ($record->isSnippet())
    	<h1>{{__('proj.Delete Practice Text')}}</h1>
    @else
    	<h1>{{__('proj.Delete Definition')}}</h1>
    @endif
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
