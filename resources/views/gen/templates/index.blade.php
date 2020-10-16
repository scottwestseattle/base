@extends('layouts.app')
@section('title', trans_choice('view.Template', 2))
@section('menu-submenu')@component('gen.templates.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('view.Template', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th></th><th>@LANG('ui.ID')</th><th>@LANG('base.Title')</th><th>@LANG('base.Description')</th><th>@LANG('ui.Created')</th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/templates/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td class="icon"><a href='/templates/publish/{{$record->id}}'>@component('components.icon', ['svg' => 'lightning'])@endcomponent</a></td>
				<td>{{$record->id}}</td>
				<td><a href="/templates/view/{{ $record->id }}">{{$record->title}}</a></td>
				<td>{{$record->description}}</td>
				<td>{{$record->created_at}}</td>
				<td class="icon"><a href='/templates/confirmdelete/{{$record->id}}'>@component('components.icon-delete')@endcomponent</span></a></td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>
	
</div>

@endsection
