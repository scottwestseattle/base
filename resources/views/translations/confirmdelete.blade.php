@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent
	
	<h1>Delete {{$title}}</h1>

	<form method="POST" action="/{{$prefix}}/delete/{{ $record->id }}">
			   
		<h3 name="title" class="">{{$record->title }}</h3>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>		
	
		<p>{{$record->permalink }}</p>

		<p>{{$record->descrption }}</p>
		
		<div class="submit-button">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>
@endsection
