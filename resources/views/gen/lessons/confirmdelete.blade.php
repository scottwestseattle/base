@extends('layouts.app')

@section('content')

<div class="container page-normal">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix, 'isAdmin' => $isAdmin])@endcomponent
	
	<h1>@LANG('ui.Delete') @LANG('content.' . $title)</h1>
	
	<h3 name="title" class="">{{$record->course->title}}: {{$record->title }}</h3>

	<form method="POST" action="/{{$prefix}}/delete/{{ $record->id }}">
			   
		<div class="form-group">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>		
	
		<p>{{$record->permalink }}</p>

		<p>{{$record->description }}</p>
		
		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>
@endsection
