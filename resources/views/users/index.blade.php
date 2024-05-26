@extends('layouts.app')
@section('title', __('base.User List'))
@section('menu-submenu')@component('users.menu-submenu')@endcomponent @endsection
@section('content')
@php
    $locale = app()->getLocale();
@endphp
<div class="">
	<h1>@LANG('ui.Users')<span class="title-count">({{count($records)}})</span></h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th></th><th>@LANG('ui.Name')</th><th>@LANG('ui.Email')</th><th>@LANG('ui.Blocked')</th>
				<th>@LANG('ui.IP')</th>
				<th>{{trans_choice('ui.Site', 1)}}</th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href="{{route('users.edit', ['locale' => $locale, 'user' => $record->id])}}">@component('components.icon-edit')@endcomponent</a></td>
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash-fill', 'href' => "/users/delete/$record->id", 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
				<td>
				    <a href="{{route('users.view', ['locale' => $locale, 'user' => $record->id])}}">{{$record->name}} ({{$record->id}})</a>
    				<div class="medium-thin-text">{{$record->created_at}}</div>
				</td>
				<td>
				    {{$record->email}}
    				<div class="medium-thin-text">@LANG('ui.' . $record->getUserType())</div>
				</td>
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
