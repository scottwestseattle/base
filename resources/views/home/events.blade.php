@extends('layouts.app')

@section('content')

	<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
		<h1>Events ({{count($records)}})</h1>
		
		<div class="ml-4 text-md text-gray-500 sm:ml-0 event-table">
		<table>
		@foreach($records as $record)
		<tr>
			<td>
				<svg class="bi mt-1 text-{{$record['color']}}" width="24" height="24" >
					<use xlink:href="/img/bootstrap-icons.svg#{{$record['icon']}}" />
				</svg>
			</td>
			<td>
				<div class="ml-2 mb-4">{{$record['text']}}</div>
			</td>
		@endforeach
		</tr>
		</table>
		</div>
		
	</div>

@endsection