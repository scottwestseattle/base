@extends('layouts.app')
@section('title', __('proj.Deleted Lessons'))
@section('menu-submenu')@component('gen.lessons.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{__('proj.Deleted Lessons')}} ({{count($records)}})</h1>
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
				<td class="index-button"><a href='/lessons/undelete/{{$record->id}}'>{{__('ui.Undelete')}}</a></td>
				<td>{{$record->title}}</td>
				<td>{{Str::limit($record->description, DESCRIPTION_LIMIT_LENGTH)}}</td>
				<td class="index-button">{{__(App\Status::getReleaseStatus($record->release_flag)['label'])}}</td>
				<td>{{$record->created_at}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
