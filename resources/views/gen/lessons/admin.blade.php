@extends('layouts.app')

@section('content')

<div class="container page-normal">
	
	@component($prefix . '.menu-submenu', ['prefix' => $prefix, 'isAdmin' => $isAdmin])@endcomponent

	@if (isset($course))
		<h1>{{$course->title}} ({{count($records)}})</h1>
	@else
		<h1>@LANG('content.Lessons') ({{count($records)}})</h1>
	@endif

	<table class="table table-responsive">
		<thead>
			<tr>
				<th></th><th></th><th>@LANG('content.Course')</th><th>@LANG('gen.Title')</th><th>@LANG('gen.Description')</th><th></th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>					
				<td><a href="/{{$prefix}}/edit/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-edit"></span></a></td>
				<td><a href="/{{$prefix}}/publish/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-publish"></span></a></td>
				
				<td><a href="/courses/view/{{$record->parent_id}}">{{$record->parent_id}}</a></td>
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->getDisplayNumber()}}&nbsp;{{$record->title}}</a></td>
				<td class="">{{substr($record->description, 0, 200)}}</td>	
				<td>
					@if ($record->isUnfinished())
					<a href="/{{$prefix}}/publish/{{$record->id}}"><button type="button" class="btn btn-xs {{$record->getStatus()['btn']}}">{{$record->getStatus()['text']}}</button></a>
					@endif
				</td>
				<td><a href="/{{$prefix}}/confirmdelete/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-delete"></span></a></td>
			</tr>
		@endforeach
		</tbody>
	</table>
               
</div>
@endsection
