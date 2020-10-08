@extends('layouts.app')
@section('title', __('view.Tester List'))
@section('menu-submenu')@component('testers.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{__('view.Testers')}} ({{count($records)}})</h1>
	
	<a href="/testers/add">{{__('view.Add Tester')}}</a>
	
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
