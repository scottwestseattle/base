@php
    $prefix = 'lessons';
    $isAdmin = isAdmin();
@endphp
@extends('layouts.app')
@section('title', __('proj.Edit Lesson') )
@section('menu-submenu')@component('gen.lessons.menu-submenu', ['prefix' => $prefix, 'record' => $record])@endcomponent @endsection
@section('content')

	<h1>@LANG('proj.Edit Lesson') - {{$record->title}}</h1>

	<form method="POST" action="/{{$prefix}}/update2/{{$record->id}}">

		<div class="form-group">
			<div class="submit-button">
				<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
			</div>
		</div>

		@component('components.control-accent-chars-esp', ['flat' => true])@endcomponent

		<textarea style="height:500px" name="text" id="text" class="form-control big-text"  onclick="setFocus($(this), '#accent-chars');">{{$record->text}}</textarea>

		<div class="submit-button mt-3">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>

@endsection
