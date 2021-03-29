@extends('layouts.app')
@section('title', trans_choice('ui.Visitor', 2))
@section('menu-submenu')@component('visitors.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('ui.Visitor', 2)}} ({{count($records)}} {{__('ui.of')}} {{$count}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>@LANG('ui.IP')</th><th>@LANG('ui.Host')</th><th>@LANG('ui.Referrer')</th><th>@LANG('ui.Created')</th><th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
				<td>{{$record->ip_address}}</td>
				<td>{{$record->host_name}}</td>
				<td>{{$record->referrer}}</td>
				<td>{{$record->created_at}}</td>
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/visitors/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
				<!-- td class="icon"><a href='/visitors/confirmdelete/{{$record->id}}'>@component('components.icon', ['svg' => 'trash-fill'])@endcomponent</a></td -->
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
