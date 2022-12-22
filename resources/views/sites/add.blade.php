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
			<label for="frontpage" class="control-label">@LANG('ui.Front Page'):</label>
			<input type="text" name="frontpage" class="form-control" value="fp-learn" />
			<p class='medium-thin-text'>Options: fp-learn, fp-language (has errors)</p>
		</div>

		<div class="form-group">
			<label for="description" class="control-label">@LANG('ui.Description'):</label>
			<input type="text" name="description" class="form-control" value="base.siteTitle-tools" />
			<p class='medium-thin-text'>Options: base.siteTitle-lunalanguage, base.siteTitle-codespace, base.siteTitle-localhost, base.siteTitle-tools, </p>
		</div>

		<div class="form-group">
			<label for="options" class="control-label">{{trans_choice('ui.Option', 2)}}:</label>
			<input type="text" name="options" class="form-control" value="articles;books-es;books-en;books-it;dictionary;fpheader;fpsteps;" />
			<p class='medium-thin-text'>Options: articles;books-es;books-en;books-it;dictionary;fpheader;fpsteps;</p>
		</div>

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('base.Add')</button>
		</div>

		{{ csrf_field() }}
	</form>

@endsection
