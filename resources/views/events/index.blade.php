@extends('layouts.app')
@section('title', trans_choice('base.Event', 2))
@section('menu-submenu')@component('events.menu-submenu')@endcomponent @endsection
@section('content')

<div class="">
	<h1>{{trans_choice('base.Event', 2)}} ({{count($records)}})</h1>
	<div class="table-responsive text-md sm:ml-0">
		<table class="table table-borderless table-striped table-events">
		@foreach($records as $record)
			<tr style="background-color: {{$record['bgColor']}};">
				<td>
					<svg class="mt-1 text-{{$record['color']}}" width="24" height="24" >
						<use xlink:href="/img/bootstrap-icons.svg#{{$record['icon']}}" />
					</svg>
				</td>
				<td>
					<div class="">{{$record['text']}}</div>
				</td>
			</tr>
		@endforeach
		</table>
	</div>
</div>

@endsection