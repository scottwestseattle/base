@extends('layouts.app')
@section('title', __('proj.Edit Definition'))
@section('menu-submenu')@component('gen.definitions.menu-submenu', ['prefix' => 'definitions', 'record' => $record])@endcomponent @endsection
@section('content')

@component('gen.definitions.component-search-toolbar', ['record' => $record, 'id' => 1, 'hideFavorites' => true])@endcomponent

<h1>{{__('proj.Edit Definition')}}</h1>

@component('components.control-accent-chars-esp', ['visible' => true, 'flat' => true])@endcomponent

<form method="POST" id="form-edit" action="/definitions/update/{{$record->id}}">

    <div class="form-group">
        <label for="title" class="control-label mb-0">@LANG('proj.Word, Phrase, or Practice Text'):</label>
        <a onclick="event.preventDefault(); $('#title').val(''); $('#title').focus();" href="" tabindex="-1" class="ml-3"><span id="" class="glyphicon glyphicon-remove" ></span></a>
        <div class="mb-1 ml-2">
            <a onclick="translateOnWebsite(event, 'deepl', $('#title').val());" href="" tabindex="-1" class="small-thin-text">DeepL</a>
            <a onclick="translateOnWebsite(event, 'spanishdict', $('#title').val());" href="" tabindex="-1"  class="small-thin-text ml-2">Span!shD¡ct</a>
            <a onclick="translateOnWebsite(event, 'rae', $('#title').val());" href="" tabindex="-1"  class="small-thin-text ml-2">RAE</a>
        </div>
        <textarea rows="3" id="title" name="title" class="form-control" autocomplete="off" onfocus="setFocus($(this), '#accent-chars');  $('#wordexists').html('');" onblur="wordExists($(this))" />{{$record->title}}</textarea>
        <div id="wordexists" class="small-thin-text ml-2 mb-0"></div>
    </div>

    <div class="form-group">
        <label for="translation_en" class="control-label">{{trans_choice('ui.Translation', 1)}}:</label>
        <textarea rows="3" name="translation_en" id="translation_en" class="form-control" autocomplete="off" onfocus="setFocus($(this))" >{{$record->translation_en}}</textarea>
    </div>

    <div class="form-group">
        @component('components.control-dropdown-menu', [
            'prompt' => __('proj.Part of Speech') . ':',
            'options' => App\Gen\Definition::getPosOptions(),
            'field_name' => 'pos_flag',
            'prompt_div' => true,
            'select_class' => 'form-control form-control-sm',
            'selected_option' => $record->pos_flag,
            'onchange' => 'onChangePos();',
        ])@endcomponent
    </div>

    @if (false)
    <div class="form-group">
        <label for="" class="control-label mb-0">{{trans_choice('ui.Category', 2)}} (@LANG('ui.optional')):</lable><br/>
        <div class="mt-1">
            <div class="float-left mr-3"><input type="checkbox" name="cat1" id="cat1" class="mr-1" /><label>Gender or Number</label></div>
            <div class="float-left mr-3"><input type="checkbox" name="cat2" id="cat2" class="mr-1" /><label>Preterite</label></div>
            <div class="float-left mr-3"><input type="checkbox" name="cat3" id="cat3" class="mr-1" /><label>Phrasing</label></div>
            <div class="float-left mr-3"><input type="checkbox" name="cat4" id="cat4" class="mr-1" /><label>Reflexive</label></div>
            <div class="float-left mr-3"><input type="checkbox" name="cat5" id="cat5" class="mr-1" /><label>Subjunctive</label></div>
            <div class="float-left mr-3"><input type="checkbox" name="cat6" id="cat6" class="mr-1" /><label>Object</label></div>
            <div class="float-left mr-3"><input type="checkbox" name="cat7" id="cat7" class="mr-1" /><label>Preposition</label></div>
            <div class="float-left mr-3"><input type="checkbox" name="cat8" id="cat8" class="mr-1" /><label>Grammar</label></div>
            <div class="float-left mr-3"><input type="checkbox" name="cat9" id="cat9" class="mr-1" /><label>Article</label></div>
        </div>
    </div>
    @endif

    <div class="form-group">
        <label for="notes" class="control-label">{{trans_choice('ui.Note', 2)}} (@LANG('ui.optional')):</label>
        <input type="text" name="notes" id="notes" class="form-control" autocomplete="off" value="{{$record->notes}}"/>
    </div>

    <div class="form-group">
        <div class="submit-button mt-2 mb-2"><button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button></div>
    </div>

    <div id="dictionary_fields" class="{{$record->isSnippet() ? 'hidden' : '' }}">

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

        <div id="div_conjugations" class="form-group {{$record->isVerb() ? '' : 'hidden'}}">
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

    </div><!-- dictionary_fields -->

    @if (isAdmin())
    <div class="form-group">
        <label for="user_id" class="control-label">@LANG('ui.User ID'):</label>
        <input type="number" min="0" name="user_id" id="user_id" class="form-control" value="{{$record->user_id}}" />
    </div>
    @endif

    <div class="form-group">
        <label for="language_flag" class="control-label">{{trans_choice('ui.Language', 1)}}:</label>
        @component('components.control-dropdown-language', [
            'options' => getLanguageOptions(),
            'selected_option' => isset($record->language_flag) ? $record->language_flag : -1,
            'field_name' => 'language_flag',
            'select_class' => 'form-control',
        ])@endcomponent
    </div>

    {{ csrf_field() }}

</form>

@endsection

<script>
    function onChangePos()
    {
        $("#pos_flag").val() == {{DEFINITIONS_POS_SNIPPET}} ? $("#dictionary_fields").hide() : $("#dictionary_fields").show();
        $("#pos_flag").val() == {{DEFINITIONS_POS_VERB}} ? $("#div_conjugations").show() : $("#div_conjugations").hide();
    }
</script>
