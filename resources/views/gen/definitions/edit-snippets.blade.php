@extends('layouts.app')
@section('title', __('proj.Edit Practice Text'))
@section('menu-submenu')@component('gen.definitions.menu-submenu-snippets', ['prefix' => 'definitions', 'record' => $record])@endcomponent @endsection
@section('content')

@component('gen.definitions.component-heart', ['record' => $record, 'id' => 1, 'lists' => $favoriteLists])@endcomponent

<h1>{{__('proj.Edit Practice Text')}}</h1>

@component('components.control-accent-chars-esp', ['visible' => true, 'flat' => true])@endcomponent

<form method="POST" id="form-edit" action="/practice/update/{{$record->id}}">

    <div class="form-group mt-3">
        <textarea rows="5" name="examples" id="examples" class="form-control" autocomplete="off" onfocus="setFocus($(this))">{{$record->examples}}</textarea>
    </div>

    <div class="form-group">
        <label for="language_flag" class="control-label">{{trans_choice('ui.Language', 1)}}:</label>
        @component('components.control-dropdown-language', [
            'options' => getLanguageOptions(),
            'selected_option' => isset($record->language_flag) ? $record->language_flag : -1,
            'field_name' => 'language_flag',
            'select_class' => 'form-control',
        ])@endcomponent
    </div>

    <div class="form-group">
        <div class="submit-button">
            <button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
        </div>
    </div>

    {{ csrf_field() }}

</form>

@endsection

