@extends('layouts.app')
@section('title', trans_choice('ui.Word', 2))
@section('menu-submenu')@component('words.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('ui.Word', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th>@LANG('base.Title')</th><th>@LANG('base.Description')</th><th></th><th>@LANG('ui.Created')</th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/words/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td><a href="/words/{{ blank($record->permalink) ? 'view/' . $record->id : $record->permalink }}">{{$record->title}}</a></td>
				<td>{{Str::limit($record->description, DESCRIPTION_LIMIT_LENGTH)}}</td>
				<td>{{substr(getLanguageName($record->language_flag), 0, 2)}}</td>
				<td>{{$record->created_at}}</td>
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/words/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
