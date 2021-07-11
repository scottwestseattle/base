@extends('layouts.app')

@section('content')

<div class="container page-normal">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix, 'isAdmin' => $isAdmin])@endcomponent

	<h1>Publish {{$title}}</h1>

	<form method="POST" action="/{{$prefix}}/publishupdate/{{ $record->id }}">

		<h3 name="title" class="">{{$record->title }}</h3>

		<div class="form-group">
			<input type="checkbox" name="finished_flag" id="finished_flag" class="form-control-inline" value="{{$record->finished_flag }}" {{ ($record->finished_flag) ? 'checked' : '' }} />
			<label for="finished_flag" class="checkbox-label">Finished</label>
		</div>
		
		<div class="form-group">
			<input type="checkbox" name="approved_flag" id="approved_flag" class="form-control-inline" value="{{$record->approved_flag }}" {{ ($record->approved_flag) ? 'checked' : '' }} />
			<label for="approved_flag" class="checkbox-label">Approved</label>
		</div>

		<div class="form-group">
			<input type="checkbox" name="published_flag" id="published_flag" class="form-control-inline" value="{{$record->published_flag }}" {{ ($record->published_flag) ? 'checked' : '' }} />
			<label for="published_flag" class="checkbox-label">Published</label>
		</div>
		
		<div class="submit-button">
			<button type="submit" class="btn btn-primary">Update</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
