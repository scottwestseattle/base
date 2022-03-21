@extends('layouts.app')
@section('title', __('proj.Edit Practice Text'))
@section('menu-submenu')@component('gen.definitions.menu-submenu-snippets', ['prefix' => 'definitions', 'record' => $record])@endcomponent @endsection
@section('content')

@component('gen.definitions.component-heart', ['record' => $record, 'id' => 1, 'lists' => $favoriteLists])@endcomponent

<h1>{{__('proj.Edit Practice Text')}}</h1>

@component('components.control-accent-chars-esp', ['visible' => true, 'flat' => true])@endcomponent

<form method="POST" id="form-edit" action="/practice/update/{{$record->id}}">

    <div class="form-group mt-3">
        <label for="title_long">@LANG('proj.Practice Text'):
            <a onclick="event.preventDefault(); $('#title_long').val(''); $('#title_long').focus();" href="" tabindex="-1" class="ml-2"><span  style="margin:0px;" class="glyphicon glyphicon-remove" ></span></a>
            <a onclick="translateOnWebsite(event, 'deepl', $('#title_long').val());" href="" tabindex="-1"  class="small-thin-text ml-2">DeepL</a>
        </label>
        <textarea rows="5" name="title_long" id="title_long" class="form-control" autocomplete="off" onfocus="setFocus($(this))">{{$record->title_long}}</textarea>
    </div>

    <div class="form-group mt-3">
        <label for="translation_en" class="bg-default">{{trans_choice('ui.Translation', 1)}}:
            <a onclick="event.preventDefault(); $('#translation_en').val(''); $('#translation_en').focus();" href="" tabindex="-1" class="ml-2"><span  style="margin:0px;" class="glyphicon glyphicon-remove" ></span></a>
        </label>
        <textarea rows="5" name="translation_en" id="translation_en" class="form-control" autocomplete="off" onfocus="setFocus($(this))">{{$record->translation_en}}</textarea>
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

