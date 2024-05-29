@extends('layouts.app')
@section('title', trans_choice('proj.Exercise', 2))
@section('menu-submenu')@component('gen.exercises.menu-submenu')@endcomponent @endsection
@section('content')
@php
    $locale = app()->getLocale();
@endphp
<div>
	<h1>{{trans_choice('proj.Exercise', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
            	<th></th><th>{{__('ui.Title')}}</th><th>{{__('proj.Flags')}}</th><th>{{__('ui.Active')}}</th><th>{{__('proj.Tpl')}}</th><th>{{__('ui.Program')}}</th><th>{{__('ui.Created')}}</th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href="{{route('exercises.edit', ['locale' => $locale, 'exercise' => $record->id])}}">@component('components.icon-edit')@endcomponent</a></td>
@if (false)
				<td class="icon"><a href="{{route('exercises.publishUpdate', ['locale' => $locale, 'exercise' => $record->id])}}">@component('components.icon', ['svg' => 'lightning'])@endcomponent</a></td>
				<td class="index-button">@component('components.button-release-status', ['record' => $record, 'views' => 'exercises', 'class' => 'btn-xxs'])@endcomponent</td>
@endif
				<td>{{$record->title}}<span class="title-count">{{$record->template_flag == 1 ? '(tpl)' : ''}}</span></td>
				<td>{{$record->type_flag}} / {{$record->subtype_flag}} / {{$record->action_flag}}</td>
				<td>{{$record->active_flag}}</td>
				<td>{{$record->template_id}}</td>
				@if (isset($record->program_id))
				    <td>{{$record->program_id}}</td>
				@else
				    <td></td>
				@endif
				<!-- td>{{Str::limit($record->url, DESCRIPTION_LIMIT_LENGTH)}}</td -->
				<td class="date-sm">{{$record->created_at}}</td>
                <td style="width:10px;" class="steel-blue">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => route('exercises.delete', ['locale' => $locale, 'exercise' => $record->id]), 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
