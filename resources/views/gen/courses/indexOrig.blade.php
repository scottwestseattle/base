@extends('layouts.app')
@section('title', trans_choice('proj.Course', 2))
@section('menu-submenu')@component('gen.courses.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('proj.Course', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
@if (true)
				<th></th><th></th><th>{{__('ui.Release')}}</th><th>{{__('ui.Title')}}</th><th>{{__('ui.Type')}}</th><th>{{__('ui.Description')}}</th><th>{{__('ui.Created')}}</th><th></th>
@else
				<th></th><th>{{__('ui.Title')}}</th><th>{{__('ui.Type')}}</th><th>{{__('ui.Description')}}</th><th>{{__('ui.Created')}}</th><th></th>
@endif
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/courses/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
@if (true)
				<td class="icon"><a href='/courses/publishupdate/{{$record->id}}'>@component('components.icon', ['svg' => 'lightning'])@endcomponent</a></td>
				<td class="index-button">@component('components.button-release-status', ['record' => $record, 'views' => 'courses', 'class' => 'btn-xxs'])@endcomponent</td>
@endif
				<td><a href="/courses/{{ blank($record->permalink) ? 'show/' . $record->id : 'view/' . $record->permalink }}">{{$record->title}}</a></td>
				<td>{{$record->type_flag}}</td>
				<td>{{Str::limit($record->description, DESCRIPTION_LIMIT_LENGTH)}}</td>
				<td class="date-sm">{{$record->created_at}}</td>
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/courses/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
				<!-- td class="icon"><a href='/courses/confirmdelete/{{$record->id}}'>@component('components.icon', ['svg' => 'trash-fill'])@endcomponent</a></td -->
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
