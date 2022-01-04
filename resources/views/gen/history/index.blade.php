@extends('layouts.app')
@section('title', trans_choice('ui.History', 2))
@section('menu-submenu')@component('gen.history.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('ui.History', 2)}}<span class="title-count">({{count($records)}})</span></h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
            @if (false && isAdmin())
				<th></th>
			@endif
				<th>{{__('proj.Program')}}</th>
				<th>{{__('ui.Session')}}</th>
				<th>{{__('proj.Score')}}</th>
				<th>{{__('ui.IP')}}</th>
				<th>{{__('ui.Created')}}</th>
            @if (isAdmin())
				<th></th>
				<th></th>
			@endif
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
		    @php
		        // we can convert the timezone but how to get the browser timezone to the server?
                //$dt = new DateTime($record->created_at, new DateTimeZone('UTC'));
                //$dt->setTimezone(new DateTimeZone('America/Denver'));
                //$dt = $dt->format('Y-m-d H:i:s T');
                $dt = $record->created_at;
                $bg = App\DateTimeEx::getDayColor($dt);
		    @endphp
			<tr style="background-color: {{$bg}}; color:white;">
            @if (false && isAdmin())
				<td class="icon"><a href='/history/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
			@endif
				<td>{{$record->program_name}}</td>
				<td>{{$record->session_name}}</td>
				<td>{{$record->seconds}}</td>
				<td>{{$record->ip_address}}</td>
				<td class="date-sm">{{App\DateTimeEx::getShortDateTime($record->created_at)}}</td>
            @if (isAdmin())
				<td class="icon"><a href='/history/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/history/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
            @endif
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
