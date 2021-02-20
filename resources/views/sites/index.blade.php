@extends('layouts.app')
@section('title', trans_choice('ui.Site', 2))
@section('menu-submenu')@component('sites.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('ui.Site', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th></th>
				<th>@LANG('ui.Release')</th>
				<th>@LANG('ui.Title')</th>
				<th>{{trans_choice('ui.Language', 2)}}</th>
				<th>@LANG('ui.Description')</th>
				<th>@LANG('ui.Created')</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/sites/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td class="icon"><a href='/sites/publishupdate/{{$record->id}}'>@component('components.icon', ['svg' => 'lightning'])@endcomponent</a></td>
				<td class="index-button">@component('components.button-release-status', ['record' => $record, 'views' => 'sites'])@endcomponent</td>
				<td><a href="/sites/view/{{ $record->id }}">{{$record->title}}</a></td>
				<td>{{getLanguageName($record->language_flag)}}</td>
				<td>{{Str::limit($record->description, DESCRIPTION_LIMIT_LENGTH)}}</td>
				<td>{{$record->created_at}}</td>
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/sites/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
				<!-- td class="icon"><a href='/sites/confirmdelete/{{$record->id}}'>@component('components.icon', ['svg' => 'trash-fill'])@endcomponent</a></td -->
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
