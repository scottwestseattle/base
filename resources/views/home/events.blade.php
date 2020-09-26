@extends('layouts.app')

@section('content')

	<div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
		<h1>Events ({{count($records)}})</h1>
		
		<div class="ml-4 text-md text-gray-500 sm:ml-0">
		@foreach($records as $record)
			<p>{{$record}}</p>
		@endforeach
		</div>
		
	</div>

@endsection