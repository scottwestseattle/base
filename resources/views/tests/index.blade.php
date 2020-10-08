@extends('layouts.app')
@section('title', __('view.Test List'))
@section('menu-submenu')@component('tests.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{__('view.Tests')}} ({{count($records)}})</h1>
	
	<a href="/tests/add">{{__('view.Add Test')}}</a>
	
	<div class="">
		@foreach($records as $record)			
		<div class="">
			<div class="" >
				<a class="" href="/{{$prefix}}/view/{{$record->id}}">{{$record->title}}</a>
				<div>{{$record->description}}</div>
			</div>		
		</div>
		@endforeach		
	</div>
</div>

@endsection
