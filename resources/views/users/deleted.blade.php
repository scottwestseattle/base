@extends('layouts.app')
@section('title', __('base.Deleted Users'))
@section('menu-submenu')@component('users.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{__('base.Deleted Users')}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th>@LANG('base.Name')</th><th>@LANG('base.Email')</th><th>@LANG('ui.Created')</th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="index-button"><a href='/users/undelete/{{$record->id}}'>{{__('ui.Undelete')}}</a></td>
				<td>{{$record->name}}</td>
				<td>{{$record->email}}</td>
				<td>{{$record->created_at}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
