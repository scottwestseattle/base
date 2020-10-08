@extends('layouts.app')
@section('title', __('view.Visitor') . ' ' . __('base.List'))
@section('menu-submenu')@component('visitors.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{__('view.Visitors')}} ({{count($records)}})</h1>
	
	<a href="/visitors/add">{{__('base.Add')}} {{__('view.Visitor')}}</a>
	
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
