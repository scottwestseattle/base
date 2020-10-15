@extends('layouts.app')
@section('title', trans_choice('view.Site', 2))
@section('menu-submenu')@component('gen.sites.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('view.Site', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th>@LANG('ui.ID')</th><th>@LANG('base.Title')</th><th>@LANG('base.Description')</th><th>@LANG('ui.Created')</th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="glyphicon-width"><a href='/users/edit/{{$record->id}}'><span class="glyphCustom-sm glyphicon glyphicon-edit"></span></a></td>
				<td>{{$record->id}}</td>
				<td><a href="/sites/view/{{ $record->id }}">{{$record->title}}</a></td>
				<td>{{$record->description}}</td>
				<td>{{$record->created_at}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>
	
</div>

@endsection
