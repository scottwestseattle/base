@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent

<div class="container">
	<h1>Delete</h1>

	<form method="POST" action="/tasks/delete/{{ $task->id }}">

		<!-- div class="form-group">
			<span name="title" class="">{{$task->title }}</span>
		</div -->

		<div class="form-group">
			<span name="description" class="">{{$task->description }}</span>	
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
