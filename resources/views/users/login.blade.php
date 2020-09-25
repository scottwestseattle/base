@extends('layouts.app')

@section('content')

<?php $isAdmin = (Auth::user()->user_type >= USER_SITE_ADMIN || Auth::user()->user_type >= USER_SUPER_ADMIN); ?>

<div class="page-size container">
	@if (Auth::check())
		<h1>Users</h1>
		<table class="table">
			<thead>
				<tr>
					<th></th><th>Name</th><th>Email</th>
					@if ($isAdmin)					
					<th>ID</th><th>User Type</th><th>Blocked</th>
					@endif
				</tr>
			</thead>
			<tbody>@foreach($records as $record)
				<tr>
					<td style="width:10px;"><a href='/users/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td><a href="/users/view/{{ $record->id }}">{{$record->name}}</a></td>
					<td>{{$record->email}}</td>
					@if ($isAdmin)
						<td>{{$record->id}}</td>
						<td>{{$record->user_type}}</td>
						<td>{{$record->blocked_flag != 0 ? 'yes' : 'no'}}</td>
					@endif
				</tr>
			@endforeach</tbody>
		</table>
	@endif               
</div>
@endsection
