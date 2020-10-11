@extends('layouts.app')
@section('title', __('view.Site') . ' ' . __('base.List'))
@section('menu-submenu')@component('gen.sites.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{__('view.Sites')}} ({{count($records)}})</h1>
	
	<a href="/sites/add">{{__('base.Add')}} {{__('view.Site')}}</a>
	
	<div class="">
		@foreach($records as $record)			
		<div class="">
			<div class="" >
				<a class="" href="/sites/view/{{$record->id}}">{{$record->title}}</a>
				<div>{{$record->description}}</div>
			</div>		
		</div>
		@endforeach		
	</div>
</div>

@endsection
