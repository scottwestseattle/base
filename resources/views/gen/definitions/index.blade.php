@extends('layouts.app')
@section('title', trans_choice('proj.Definition', 2))
@section('menu-submenu')@component('gen.definitions.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('proj.Definition', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th>@LANG('ui.Title')</th><th>{{trans_choice('proj.Definition', 2)}}</th><th>@LANG('ui.Created')</th>
				@if (isAdmin())
				<th></th>
				@endif
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/definitions/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td><a href="/definitions/{{ blank($record->permalink) ? 'view/' . $record->id : $record->permalink }}">{{$record->title}}</a></td>
				<td>{{$record->definition}}</td>
				<td class="date-sm">{{$record->created_at}}</td>
                @if (isAdmin())
    				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/definitions/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
                @endif
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
