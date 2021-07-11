@extends('layouts.app')
@section('title', trans_choice('ui.History', 2))
@section('menu-submenu')@component('gen.history.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('ui.History', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
@if (false)
				<th></th><th></th><th>{{__('ui.Release')}}</th><th>{{__('ui.Title')}}</th><th>{{__('ui.Type')}}</th><th>{{__('ui.Description')}}</th><th>{{__('ui.Created')}}</th><th></th>
@else
				<th></th><th>{{trans_choice('ui.User', 1)}}</th><th>{{__('proj.Program')}}</th><th>{{__('ui.Session')}}</th><th>{{__('ui.IP')}}</th><th>{{__('ui.Created')}}</th>
@endif
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>

				<td class="icon"><a href='/histories/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td>{{$record->user_id}}</td>
				<td><a href="/histories/{{ blank($record->program_name) ? 'show/' . $record->id : 'view/' . $record->program_name }}">{{$record->program_name}}</a></td>
				<td>{{$record->session_name}}</td>
				<td>{{$record->ip_address}}</td>
				<td class="date-sm">{{$record->created_at}}</td>
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/histories/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
				<!-- td class="icon"><a href='/histories/confirmdelete/{{$record->id}}'>@component('components.icon', ['svg' => 'trash-fill'])@endcomponent</a></td -->
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
