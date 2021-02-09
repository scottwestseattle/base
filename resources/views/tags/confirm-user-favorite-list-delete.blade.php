@extends('layouts.app')

@section('content')

<div class="container page-normal">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix, 'isAdmin' => $isAdmin])@endcomponent

	<h1>@LANG('ui.Delete') {{$record->name}}</h1>

	<form method="POST" action="/tags/delete/{{$record->id}}">

		@if ($count > 0)

			<h3 class="red">@LANG('content.This list has') {{$count}} {{trans_choice('ui.Entry')}}</h3>
			<div class="large-text mb-2">@LANG('proj.Are you sure you want to delete it?')</div>

		@endif

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

		{{ csrf_field() }}
		{{$referrer}}
	</form>
</div>
@endsection
