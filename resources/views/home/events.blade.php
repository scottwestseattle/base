@extends('layouts.app')
@section('title', 'Events')
@section('menu-submenu')@component('home.menu-submenu-events')@endcomponent @endsection
@section('content')

<div class="">
	<h1>Events ({{count($records)}})</h1>
	<div class="table-responsive text-md sm:ml-0">
		<table class="table table-borderless table-striped table-events">
		@foreach($records as $record)
			<tr>
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