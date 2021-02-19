@extends('layouts.app')

@section('content')

<div class="container page-normal">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix, 'isAdmin' => $isAdmin])@endcomponent

	<h1>Publish {{$title}}</h1>

	<form method="POST" action="/{{$prefix}}/publishupdate/{{ $record->id }}">

		<h3 name="title" class="">{{$record->title }}</h3>

		<div class="form-group">
			<label for="wip_flag" class="control-label">@LANG('content.Work Status'):</label>
			<select name="wip_flag" class="form-control">
				@foreach ($wip_flags as $key => $value)
					<option value="{{$key}}" {{ $key == $record->wip_flag ? 'selected' : ''}}>{{$value}}</option>
				@endforeach
			</select>
		</div>
		
		<div class="form-group">
			<label for="release_flag" class="control-label">@LANG('content.Release Status'):</label>
			<select name="release_flag" class="form-control">
				@foreach ($release_flags as $key => $value)
					<option value="{{$key}}" {{ $key == $record->release_flag ? 'selected' : ''}}>{{$value}}</option>
				@endforeach
			</select>
		</div>
		
		<div class="submit-button">
			<button type="submit" class="btn btn-primary">Update</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
