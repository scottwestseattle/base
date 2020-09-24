@extends('layouts.app')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent

	<h1>Add {{$title}}</h1>
               
	<form method="POST" action="/{{$prefix}}/create">
							
		<label for="title" class="control-label">Title:</label>
		<input type="text" name="title" class="form-control" />
			
		<label for="permalink" class="control-label">Permalink:</label>
		<input type="text" name="permalink" class="form-control" />

		<label for="description" class="control-label">Description:</label>
		<textarea name="description" class="form-control"></textarea>

		<div class="form-group">
			<input type="checkbox" name="published_flag" id="published_flag" class="form-control-inline" />
			<label for="published_flag" class="checkbox-label">Published</label>
		</div>
			
		<div class="form-group">
			<div class="radio-group-item">
				<input type="radio" name="radio_sample" value="1" class="form-control-inline">
				<label for="radio_sample" class="radio-label">Sample Radio Option 1</label>
			</div>
			
			<div class="radio-group-item">
				<input type="radio" name="radio_sample" value="2" class="form-control-inline">
				<label for="radio_sample" class="radio-label">Sample Radio Option 2</label>			
			</div>
		</div>
		
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Add</button>
		</div>
						
		{{ csrf_field() }}

	</form>

</div>

@endsection
