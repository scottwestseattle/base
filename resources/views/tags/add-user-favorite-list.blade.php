@extends('layouts.app')
@section('title', __('base.Add List'))
@section('menu-submenu')@component('tags.menu-submenu', ['prefix' => 'tags'])@endcomponent @endsection
@section('content')

<div class="container page-normal">

	<h1>@LANG('base.Add List')</h1>

	<form method="POST" action="/tags/create-user-favorite-list">

		<div class="form-group">
			<label for="name" class="control-label">@LANG('ui.Name'):</label>
			<input type="text" name="name" class="form-control" autofocus />
		</div>

		<div class="form-group">
			<div class="submit-button">
				<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Add')</button>
			</div>
		</div>

        @if (false)
        @component('shared.text-translation', ['record' => null])@endcomponent
        @endif

		{{ csrf_field() }}

	</form>

</div>

@endsection
