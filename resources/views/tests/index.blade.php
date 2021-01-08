@extends('layouts.app')
@section('title', __('view.Test List'))
@section('menu-submenu')@component('tests.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{__('view.Tests')}} ({{count($records)}})</h1>
	
	<a href="/tests/add">{{__('view.Add Test')}}</a>
	
	<div class="row mb-2">
		@foreach($records as $record)			
		<div class="col-sm-4"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
			<div class="drop-box" style="height:200px;  background-color: #4993FD; color:white;" ><!-- inner col div -->
				<a style="background-color: #4993FD; height:100%; width:100%;" class="btn btn-primary btn-lg" role="button" href="/{{$prefix}}/view/{{$record->id}}">
						{{$record->title}}<br/>{{$record->description}}
				</a>
			</div>		
		</div>
		@endforeach		
	</div>
</div>

@endsection
