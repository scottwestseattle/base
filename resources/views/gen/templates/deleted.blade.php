@extends('layouts.app')
@section('title', __('proj.Deleted Templates'))
@section('menu-submenu')@component('gen.templates.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{__('proj.Deleted Templates')}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th>@LANG('ui.Title')</th><th>@LANG('ui.Description')</th><th>@LANG('ui.Release')</th><th>@LANG('ui.Created')</th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="index-button"><a href='/templates/undelete/{{$record->id}}'>{{__('ui.Undelete')}}</a></td>
				<td>{{$record->title}}</td>
				<td>{{Str::limit($record->description, DESCRIPTION_LIMIT_LENGTH)}}</td>
				<td class="index-button">{{__(getReleaseStatus($record->release_flag)['label'])}}</td>
				<td>{{$record->created_at}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
