@extends('layouts.app')
@section('title', trans_choice('view.Site', 2))
@section('menu-submenu')@component('gen.sites.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('view.Site', 2)}} ({{count($records)}})</h1>
	
	<a href="/sites/add">{{__('base.Add')}} {{trans_choice('view.Site', 1)}}</a>
	
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
