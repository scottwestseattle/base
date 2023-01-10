@extends('layouts.app')
@section('title', __('base.Edit Entry'))
@section('menu-submenu')@component('entries.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('base.Edit Entry')}}</h1>

	<form method="POST" id="form-edit" action="/entries/update/{{$record->id}}">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('base.Title'):</label>
			<input type="text" name="title" class="form-control" value="{{$record->title}}"></input>
		</div>

		<div class="form-group">
			<label for="title" class="control-label">@LANG('proj.Source'):</label>
			<input type="text" name="title" class="form-control" value="{{$record->source}}"></input>
		</div>

		<div class="form-group">
			<label for="description" class="control-label">@LANG('ui.Description'):</label>
			<textarea name="description" class="form-control">{{$record->description}}</textarea>
		</div>

		@component('components.control-entry-types', [
		    'current_type' => $record->type_flag,
		    'entryTypes' => App\Entry::getEntryTypes()
		    ])@endcomponent

		<div class="form-group">
			<label for="description" class="control-label">@LANG('ui.User ID'):</label>
			<input type="text" name="id" class="form-control" value="{{$record->user_id}}"></input>
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
            <label for="options" class="control-label">{{trans_choice('ui.Option', 2)}}:</label>
            <input type="text" name="options" class="form-control" value="{{$record->options}}" />
            <p class='medium-thin-text'>Options: read-random;read-reverse;</p>
        </div>


		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

