@extends('layouts.app')
@section('title', __('view.Delete Template'))
@section('menu-submenu')@component('gen.templates.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
<div class="container page-normal">
	
	<h1>@LANG('view.Delete Template')</h1>
	<form method="POST" action="/templates/delete/{{ $record->id }}">
			   
		<h4>{{$record->title}}</h4>
	
		<p>{{$record->description }}</p>
		
		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>
@endsection
