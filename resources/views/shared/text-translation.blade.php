@php
    $description = isset($record->description) ? $record->description : '';
    $description_translation = isset($record->description_translation) ? $record->description_translation : '';
    $sentences = isset($sentences) ? $sentences : '';
    $sentences_translation = isset($sentences_translation) ? $sentences_translation : '';
@endphp

<div>
    <div class="mb-3">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a id="nav-link-tab1" class="nav-link active" href="#" onclick="setTab(event, 1);"><span class="nav-link-tab">@LANG('ui.Text')</span></a>
            </li>
            <li class="nav-item">
                <a id="nav-link-tab2" class="nav-link" href="#" onclick="setTab(event, 2);"><span class="nav-link-tab">{{trans_choice('ui.Translation', 1)}}</span></a>
            </li>
            <li class="nav-item">
                <a id="nav-link-tab3" class="nav-link" href="#" onclick="setTab(event, 3);"><span class="nav-link-tab">{{trans_choice('ui.Favorite', 2)}}</span></a>
            </li>
        </ul>

        <div id="tab-tab1" style="clear:both; display:default;">
            <ul class="nav">
                <li class="ml-2"><a id="flash1" onclick="clipboardCopy(event, 'flash1', 'description', false)" href="" tabindex="-1" class="small-thin-text">@LANG('ui.Copy')</a></li>
                <li class="ml-2"><a id="flash1a" onclick="event.preventDefault(); $('#description').val(getSentences($('#description').val())); $('#flash1a').css('color', 'red');" href="" tabindex="-1" class="small-thin-text">@LANG('proj.Split Sentences')</a></li>
                <li class="ml-2"><a href="" onclick="event.preventDefault(); $('#description').val(''); $('#description').focus();" class="small-thin-text ml-1">@LANG('ui.Clear')<a/></li>
                <li class="ml-2"><a href="" onclick="event.preventDefault(); swap('description', 'description_translation');" class="small-thin-text ml-1">@LANG('ui.Swap')<a/></li>
            </ul>
            <textarea rows="20" name="description" id="description" class="form-control big-text">{{$description}}</textarea>
            <textarea rows="20" name="sentences" id="sentences" class="form-control big-text hidden">{{$sentences}}</textarea>
        </div>
        <div id="tab-tab2" style="clear:both; display:none;">
            <ul class="nav">
                <li class="ml-2"><a id="flash2" onclick="clipboardCopy(event, 'flash2', 'description_translation', false)" href="" tabindex="-1" class="small-thin-text">@LANG('ui.Copy')</a></li>
                <li class="ml-2"><a id="flash2a" onclick="event.preventDefault(); $('#description_translation').val($('#sentences_translation').val()); $('#flash2a').css('color', 'red');" href="" tabindex="-1" class="small-thin-text">@LANG('proj.Split Sentences')</a></li>
                <li class="ml-2"><a href="" onclick="event.preventDefault(); $('#description_translation').val(''); $('#description_translation').focus();" class="small-thin-text ml-1">@LANG('ui.Clear')<a/></li>
                <li class="ml-2"><a href="" onclick="event.preventDefault(); swap('description', 'description_translation');" class="small-thin-text ml-1">@LANG('ui.Swap')<a/></li>
            </ul>
            <textarea rows="20" name="description_translation" id="description_translation" class="form-control big-text">{{$description_translation}}</textarea>
            <textarea rows="20" name="sentences_translation" id="sentences_translation" class="form-control big-text hidden">{{$sentences_translation}}</textarea>
        </div>
        <div id="tab-tab3" class="pt-2" style="clear:both; display:none; min-height:500px; overflow-y:auto;">
            <!-- List of flashcards - will be replaced by ajax on every click -->
        </div>
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
</div>

