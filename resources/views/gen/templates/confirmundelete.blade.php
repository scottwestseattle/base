@extends('layouts.app')
@section('title', __('view.Undelete Template'))
@section('menu-submenu')@component('gen.templates.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
<div class="container page-normal">
	
	<h1>@LANG('view.Undelete Template')</h1>
	<form method="POST" action="/templates/undelete/{{ $record->id }}">
			   
		<h4>{{$record->title}}</h4>
	
		<p>{{$record->description }}</p>
		
		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Undelete')</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>
@endsection
