@extends('layouts.app')
@section('title', __('view.Template List'))
@section('menu-submenu')@component('templates.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{__('view.Templates')}} ({{count($records)}})</h1>

	<a href="/templates/add">{{__('view.Add Template')}}</a>

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
