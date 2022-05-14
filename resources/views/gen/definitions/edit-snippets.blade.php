@extends('layouts.app')
@section('title', __('proj.Edit Practice Text'))
@section('menu-submenu')@component('gen.definitions.menu-submenu-snippets', ['prefix' => 'definitions', 'record' => $record])@endcomponent @endsection
@section('content')

@component('gen.definitions.component-heart', ['record' => $record, 'id' => 1, 'lists' => $favoriteLists])@endcomponent

<h1>{{__('proj.Edit Practice Text')}}</h1>

@component('components.control-accent-chars-esp', ['visible' => true, 'flat' => true])@endcomponent

<form method="POST" id="form-edit" action="/definitions/update/{{$record->id}}">

    <div class="form-group mt-3">
        <label for="title">@LANG('proj.Word, Phrase, or Practice Text'):
            <a onclick="event.preventDefault(); $('#title').val(''); $('#title').focus();" href="" tabindex="-1" class="ml-2"><span  style="margin:0px;" class="glyphicon glyphicon-remove" ></span></a>
            <a onclick="translateOnWebsite(event, 'deepl', $('#title').val());" href="" tabindex="-1"  class="small-thin-text ml-2">DeepL</a>
        </label>
        <textarea rows="2" name="title" id="title" class="form-control" autocomplete="off" onfocus="setFocus($(this))">{{$record->title}}</textarea>
    </div>

    <div class="form-group mt-3">
        <label for="translation_en" class="bg-default">{{trans_choice('ui.Translation', 1)}}:
            <a onclick="event.preventDefault(); $('#translation_en').val(''); $('#translation_en').focus();" href="" tabindex="-1" class="ml-2"><span  style="margin:0px;" class="glyphicon glyphicon-remove" ></span></a>
        </label>
        <textarea rows="2" name="translation_en" id="translation_en" class="form-control" autocomplete="off" onfocus="setFocus($(this))">{{$record->translation_en}}</textarea>
    </div>

    <div class="form-group">
        @component('components.control-dropdown-menu', [
            'prompt' => __('proj.Part of Speech') . ':',
            'options' => App\Gen\Definition::getPosOptions(),
            'field_name' => 'pos_flag',
            'prompt_div' => true,
            'select_class' => 'form-control form-control-sm',
            'selected_option' => $record->pos_flag,
        ])@endcomponent
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

    <div id="dictionary_fields">

        <div class="form-group">
            <label for="forms" class="control-label mr-3">@LANG('proj.Word Forms'): <span class="small-thin-text">(comma or semi-colon)</span></label>
            <a onclick="wordFormsGen(event, '#title', '#forms', true);" href="" tabindex="-1" class="ml-2"><div class="middle mb-2"><b>+s</b></div></a>
            <a onclick="wordFormsGen(event, '#title', '#forms');" href="" tabindex="-1" class="ml-2"><span class="glyphicon glyphicon-plus-sign" ></span></a>
            <a onclick="event.preventDefault(); $('#forms').val(''); $('#forms').focus();" href="" tabindex="-1" class="ml-2"><span id="button-clear" class="glyphicon glyphicon-remove" ></span></a>
            <input type="text" name="forms" id="forms" class="form-control" autocomplete="off" onfocus="setFocus($(this), '#accent-chars')" value="{{$formsPretty}}" />
            <div class="small-thin-text mb-0 ml-2">{{$record->forms}}</div>
        </div>

        <div class="form-group">
            <label for="definition" class="control-label">{{trans_choice('proj.Definition', 1)}}:</label>
            <a onclick="scrapeDefinition(event, '#title', '#definition');" href="" tabindex="-1" class="ml-2"><span id="button-increment-line" class="glyphicon glyphicon-plus-sign" ></span></a>
            <a onclick="event.preventDefault(); $('#definition').val(''); $('#definition').focus();" href="" tabindex="-1" class="ml-2"><span id="button-clear" class="glyphicon glyphicon-remove" ></span></a>
            <textarea rows="3" name="definition" id="definition" class="form-control" autocomplete="off" onfocus="setFocus($(this), '#accent-chars')" >{{$record->definition}}</textarea>
        </div>

        <div class="form-group">
            <label for="examples" class="control-label">@LANG('proj.Examples'):</label>
            <textarea rows="3" name="examples" id="examples" class="form-control" autocomplete="off" onfocus="setFocus($(this), '#accent-chars')">{{$record->examples}}</textarea>
        </div>

        <div class="form-group">
            <label for="conjugations" class="control-label mr-3">{{trans_choice('proj.Conjugation', 2)}}:</label>
            <a onclick="event.preventDefault(); conjugationsGen('#title', '#conjugations');" href="" tabindex="-1" class="ml-2"><span id="button-increment-line" class="glyphicon glyphicon-plus-sign" ></span></a>
            <a onclick="event.preventDefault(); $('#conjugations').val(''); $('#conjugations').focus();" href="" tabindex="-1" class="ml-2"><span id="button-clear" class="glyphicon glyphicon-remove" ></span></a>
            <textarea rows="7" name="conjugations" id="conjugations" class="form-control" autocomplete="off" onfocus="setFocus($(this))" >{{$record->conjugations}}</textarea>
            <div class="small-thin-text mb-2 wordwrap m-1">{{$record->conjugations_search}}</div>
        </div>

        <div class="form-group">
            <label for="rank" class="control-label">@LANG('proj.Rank'):</label>
            <input type="number" min="0" name="rank" id="rank" class="form-control" value="{{$record->rank}}" />
        </div>

        <div class="form-group">
            <div class="submit-button">
                <button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
            </div>
        </div>

    </div>

    {{ csrf_field() }}

</form>

@endsection

