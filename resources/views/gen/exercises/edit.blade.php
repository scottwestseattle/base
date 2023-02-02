@extends('layouts.app')
@section('title', __('proj.Edit Exercise'))
@section('menu-submenu')@component('gen.exercises.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('proj.Edit Exercise')}}</h1>

	<form method="POST" id="form-edit" action="/exercises/update/{{$record->id}}">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('base.Title'):</label>
			<input type="text" name="title" value="{{$record->title}}" class="form-control @error('model') is-invalid @enderror" required autofocus />
			@error('title')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
        </div>

		<div class="form-group">
		@component('components.control-dropdown-menu', ['record' => $record, 'prefix' => 'Exercise',
			'isAdmin' => isAdmin(),
			'prompt' => __('proj.Content Type'),
			'empty' => __('ui.Select Item'),
			'options' => App\Gen\Exercise::getTypes(),
			'selected_option' => $record->type_flag,
			'field_name' => 'type_flag',
			'prompt_div' => true,
			'select_class' => 'form-control-md',
		])@endcomponent
		</div>

		<div class="form-group">
		@component('components.control-dropdown-menu', ['record' => $record, 'prefix' => 'Exercise',
			'isAdmin' => isAdmin(),
			'prompt' => __('proj.Content Order'),
			'empty' => __('ui.Select Item'),
			'options' => App\Gen\Exercise::getSubtypes(),
			'selected_option' => $record->subtype_flag,
			'field_name' => 'subtype_flag',
			'prompt_div' => true,
			'select_class' => 'form-control-md',
		])@endcomponent
		</div>

		<div class="form-group">
		@component('components.control-dropdown-menu', ['record' => $record, 'prefix' => 'Exercise',
			'isAdmin' => isAdmin(),
			'prompt' => __('ui.Action'),
			'empty' => __('ui.Select Item'),
			'options' => App\Gen\Exercise::getActions(),
			'selected_option' => $record->action_flag,
			'field_name' => 'action_flag',
			'prompt_div' => true,
			'select_class' => 'form-control-md',
		])@endcomponent
		</div>

        @if (isset($languageOptions))
            <div><labe>{{trans_choice('ui.Language', 1)}}:</label></div>
            @component('components.control-dropdown-language', [
                'options' => $languageOptions,
                'selected_option' => $record->language_flag,
                'field_name' => 'language_flag',
                'select_class' => 'mt-1 mb-3',
            ])@endcomponent
        @endif

		<div class="form-group">
			<label for="url" class="control-label">@LANG('ui.URL'):</label>
			<input type="text" name="url" value="{{$record->url}}" class="form-control" />
        </div>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

