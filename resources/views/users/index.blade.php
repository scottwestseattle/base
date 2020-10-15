@extends('layouts.app')
@section('title', __('base.User List'))
@section('menu-submenu')@component('users.menu-submenu')@endcomponent @endsection
@section('content')
<div class="">
	<h1>@LANG('ui.Users') ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th>@LANG('ui.Name')</th><th>@LANG('ui.Email')</th><th>@LANG('ui.Type')</th><th>@LANG('ui.Blocked')</th>
				<th>@LANG('ui.IP')</th>
				<th>@LANG('ui.Site')</th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/users/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td><a href="/users/view/{{ $record->id }}">{{$record->name}} ({{$record->id}})</a></td>
				<td>{{$record->email}}</td>
				<td>@LANG('ui.' . $record->getUserType())</td>
				<td>@LANG('ui.' . $record->getBlocked())</td>
				<td>{{$record->ip_register}}</td>
				<td>{{$record->site_id}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>
</div>
@endsection
