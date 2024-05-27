@extends('layouts.app')
@section('title', __('base.Edit Tag'))
@section('menu-submenu')@component('tags.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('proj.Edit ' . $record->getTypeFlagName())}}</h1>

	<form method="POST" id="form-edit" action="{{route('tags.update', ['locale' => app()->getLocale(), 'tag' => $record->id])}}">

		<div class="form-group">
			<label for="name" class="control-label">@LANG('ui.Name'):</label>
			<input type="text" name="name" class="form-control" value="{{$record->name}}"></input>
		</div>

        @if (isAdmin())
            <div class="form-group">
                <label for="type_flag" class="control-label">@LANG('ui.Type'):</label>
                <input type="text" name="type_flag" class="form-control" value="{{$record->type_flag}}"></input>
            </div>

            <div class="form-group">
                <label for="user_id" class="control-label">{{trans_choice('ui.User', 1)}}:</label>
                <input type="text" name="user_id" class="form-control" value="{{$record->user_id}}"></input>
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

		@endif

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

