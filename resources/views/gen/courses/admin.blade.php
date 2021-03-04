@php
    $prefix = 'courses';
@endphp
@extends('layouts.app')
@section('title', __('ui.Admin') )
@section('menu-submenu')@component('gen.courses.menu-submenu', ['prefix' => $prefix])@endcomponent @endsection
@section('content')

	<h1>{{trans_choice('proj.Course', 2)}} ({{count($records)}})</h1>

	<table class="table table-responsive">
		<thead>
			<tr>
				<th></th><th></th><th>@LANG('ui.Title')</th><th>@LANG('ui.Description')</th><th></th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td><a href="/{{$prefix}}/edit/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-edit"></span></a></td>
				<td><a href="/{{$prefix}}/publish/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-publish"></span></a></td>
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->title}}</a>
				<div>
					@if (!$record->isFinished())
						<a href="/{{$prefix}}/publish/{{$record->id}}"><button type="button" class="btn btn-xs {{App\Status::getWipStatus($record->wip_flag)['btn']}}">{{__(App\Status::getWipStatus($record->wip_flag)['text'])}}</button></a>
					@endif
					@if (!$record->isPublic())
						<a href="/{{$prefix}}/publish/{{$record->id}}"><button type="button" class="btn btn-xs {{$record->getStatus()['btn']}}">{{__($record->getStatus()['text'])}}</button></a>
					@endif
				</div>
				</td>
				<td>{{substr($record->description, 0, 200)}}</td>
				<td><a href="/{{$prefix}}/edit/{{$record->id}}">{{$record->display_order}}</a></td>
				<td><a href="/{{$prefix}}/confirmdelete/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-delete"></span></a></td>
			</tr>
		@endforeach
		</tbody>
	</table>

</div>
@endsection
