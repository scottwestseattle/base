@extends('layouts.app')
@section('title', __('proj.Add'))
@section('menu-submenu')@component('sites.menu-submenu', ['prefix' => 'sites'])@endcomponent @endsection
@section('content')

	<h1>{{__('proj.Add Site')}}</h1>
	<form method="POST" action="/sites/create">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('base.Title'):</label>
			<input type="text" name="title" class="form-control @error('model') is-invalid @enderror" required autofocus />
			@error('title')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
		</div>

		<div class="form-group">
			<label for="language_flag" class="control-label">@LANG('ui.Language'):</label>
            @component('components.control-dropdown-language', [
                'options' => $languages,
                'selected_option' =>  -1,
                'field_name' => 'language_flag',
                'select_class' => 'form-control',
            ])@endcomponent
		</div>

		<div class="form-group">
			<label for="frontpage" class="control-label">@LANG('view.Frontpage'):</label>
			<input type="text" name="frontpage" class="form-control" placeholder="{{__('view.Enter frontpage view file name')}}" />
		</div>

		<div class="form-group">
			<label for="description" class="control-label">@LANG('base.Description'):</label>
			<textarea name="description" class="form-control"></textarea>
		<div>

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('base.Add')</button>
		</div>

		{{ csrf_field() }}
	</form>

@endsection
