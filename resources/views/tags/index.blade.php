@extends('layouts.app')
@section('title', trans_choice('ui.Tag', 2))
@section('menu-submenu')@component('tags.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('ui.Tag', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th>@LANG('ui.Name')</th><th>@LANG('ui.Created')</th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/tags/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td><a href="/tags/view/{{$record->id}}">{{$record->name}}</a></td>
				<td>{{$record->created_at}}</td>
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/tags/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
