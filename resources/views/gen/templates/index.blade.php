@extends('layouts.app')
@section('title', trans_choice('view.Template', 2))
@section('menu-submenu')@component('gen.templates.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('view.Template', 2)}} ({{count($records)}})</h1>
	
	<a href="/templates/add">{{__('base.Add')}} {{trans_choice('view.Template', 1)}}</a>
	
	<div class="">
		@foreach($records as $record)			
		<div class="">
			<div class="" >
				<a class="" href="/templates/view/{{$record->id}}">{{$record->title}}</a>
				<div>{{$record->description}}</div>
			</div>		
		</div>
		@endforeach		
	</div>
</div>

@endsection
