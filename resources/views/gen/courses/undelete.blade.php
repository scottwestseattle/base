@extends('layouts.app')

@section('content')

<div class="container page-normal">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix, 'isAdmin' => $isAdmin])@endcomponent

	<h1>@LANG('ui.Undelete')&nbsp;@LANG('content.' . $titlePlural) ({{count($records)}})</h1>

	<table class="table table-responsive">
		<thead>
			<tr>
				<th></th><th>@LANG('gen.Title')</th><th>@LANG('gen.Description')</th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td><a href="/{{$prefix}}/undelete/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-undelete"></span></a></td>
				<td style="width:25%;"><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->title}}</a></td>
				<td>{{$record->description}}</td>				
			</tr>
		@endforeach
		</tbody>
	</table>
               
</div>
@endsection
