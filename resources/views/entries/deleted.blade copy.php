@extends('layouts.app')
@section('title', __('ui.Deleted'))
@section('menu-submenu')@component('entries.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>@LANG('ui.Deleted') ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th>@LANG('ui.ID')</th><th>@LANG('base.Title')</th><th>@LANG('base.Description')</th><th>@LANG('ui.Created')</th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/entries/undelete/{{$record->id}}'>Undelete</a></td>
				<td>{{$record->id}}</td>
				<td>{{$record->title}}</td>
				<td>{{$record->description}}</td>
				<td>{{$record->created_at}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
