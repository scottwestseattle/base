@extends('layouts.app')
@section('title', trans_choice('ui.Contact', 2))
@section('menu-submenu')@component('gen.contacts.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('ui.Contact', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th>{{__('ui.Name')}}</th>
				<th>{{__('ui.Account')}}</th>
				<th>{{__('ui.Updated')}}</th>
@if (false)
				<th>{{__('ui.Notes')}}</th>
@endif
				<th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/contacts/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td><a href="/contacts/show/{{$record->id}}">{{$record->name}}</a></td>
				<td>{{$record->access}}</td>
				<td>{{$record->lastUpdated}}</td>
@if (false)
				<td>{{Str::limit($record->notes, DESCRIPTION_LIMIT_LENGTH)}}</td>
@endif
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/contacts/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
				<!-- td class="icon"><a href='/contacts/confirmdelete/{{$record->id}}'>@component('components.icon', ['svg' => 'trash-fill'])@endcomponent</a></td -->
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
