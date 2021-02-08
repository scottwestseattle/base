@extends('layouts.app')
@section('title', trans_choice('proj.Book', 2))
@section('menu-submenu')@component('gen.books.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('proj.Book', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
@if (false)
				<th></th><th></th><th>{{__('ui.Release')}}</th><th>{{__('ui.Title')}}</th><th>{{__('ui.Description')}}</th><th>{{__('ui.Created')}}</th><th></th>
@endif
				<th></th><th>{{__('ui.Title')}}</th><th>{{__('ui.Description')}}</th><th>{{__('ui.Created')}}</th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/books/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
@if (false)
				<td class="icon"><a href='/books/publishupdate/{{$record->id}}'>@component('components.icon', ['svg' => 'lightning'])@endcomponent</a></td>
				<td class="index-button">@component('components.button-release-status', ['record' => $record, 'views' => 'books', 'class' => 'btn-xxs'])@endcomponent</td>
@endif
				<td><a href="/books/{{ blank($record->permalink) ? 'view/' . $record->id : $record->permalink }}">{{$record->title}}</a></td>
				<td>{{Str::limit($record->description, DESCRIPTION_LIMIT_LENGTH)}}</td>
				<td>{{$record->created_at}}</td>
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/books/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
				<!-- td class="icon"><a href='/books/confirmdelete/{{$record->id}}'>@component('components.icon', ['svg' => 'trash-fill'])@endcomponent</a></td -->
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
