@extends('layouts.app')

@section('content')

@component('users.menu-submenu')@endcomponent

<div class="container page-normal">
               
	<h1 name="name" class="">{{ $user->name }} ({{ $user->id }})</h1>

	<table style="font-size:1.2em;">
		<tr><td>@LANG('ui.Email'):</td><td><b>{{$user->email}}</b></td></tr>
		<tr><td>@LANG('ui.Type'):</td><td><b>@LANG('ui.' . $user->getUserType()) ({{$user->user_type}})</b></td></tr>
		<tr><td>@LANG('ui.Blocked'):</td><td><b>@LANG('ui.' . $user->getBlocked())</b></td></tr>
		<tr><td>@LANG('ui.Created'):</td><td><b>{{$user->created_at}}</b></td></tr>
		<tr><td>@LANG('ui.Updated'):</td><td><b>{{$user->updated_at}}</b></td></tr>
	</table>

</div>

@endsection
