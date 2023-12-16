@php
    $locale = app()->getLocale();
@endphp
@extends('layouts.app')
@section('title', __('proj.Edit Article'))
@section('menu-submenu')@component('gen.articles.menu-submenu', ['locale' => $locale, 'record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>Edit</h1>

	<form method="POST" action="{{route('articles.update', ['locale' => $locale, 'entry' => $record->id])}}">
		<div class="form-group form-control-big">

            @component('components.control-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent
			@component('components.control-accent-chars-esp', ['visible' => true, 'flat' => true])@endcomponent

			<input type="hidden" name="referer" value={{array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER["HTTP_REFERER"] : ''}} />

			<div id="en" style="display:default;">

                @if (isAdmin())
                    <div class="mb-1">
                        <label class="tiny">@LANG('ui.Title'):</label>
                        <input onblur="javascript:urlEncode('title', 'permalink')" type="text" id="title" name="title" class="form-control" onfocus="setFocus($(this), '#accent-chars')" value="{{ $record->title }}"  placeholder="Title" />
                    </div>

                    <div class="mb-1" style="font-size:.6em;">
                        <a tabindex="-1" href='#' onclick="javascript:urlEncode('title', 'permalink')";>
                            <span id="" class="glyphCustom glyphicon glyphicon-link" style="font-size:1.3em; margin-left:5px;"></span>
                        </a>
                    </div>

                    <div class="entry-title-div mb-3">
                        <input tabindex="-1" type="text" id="permalink" name="permalink" class="form-control" value="{{ $record->permalink }}"  placeholder="Permalink" />
                    </div>
				@else

                    <div class="mb-1">
                        <label class="tiny">@LANG('ui.Title'):</label>
                        <input type="text" id="title" name="title" class="form-control" onfocus="setFocus($(this), '#accent-chars')" value="{{ $record->title }}"  placeholder="Title" />
                    </div>

                @endif

                <div style="clear:both;">
                    <div class="my-2 float-left">
                        <button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
                    </div>

                    <div class="form-check m-2 pt-2 ml-3 float-left">
                        <input class="form-check-input middle" type="checkbox" name="read_reverse" {{$record->hasOption(OPTION_READ_REVERSE) ? 'checked' : ''}}>
                        <label class="form-check-label" for="read_reverse">Read Reverse</label>
                    </div>

                    <div class="form-check m-2 float-left pt-2">
                        <input class="form-check-input" type="checkbox" name="read_random" {{$record->hasOption(OPTION_READ_RANDOM) ? 'checked' : ''}}>
                        <label class="form-check-label" for="read_random"">Read Random</label>
                    </div>
                </div>

                <div class="mb-3" style="clear:both;">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a id="nav-link-tab1" class="nav-link active" href="#" onclick="setTab(event, 1);"><span class="nav-link-tab">@LANG('ui.Text')</span></a>
                        </li>
                        <li class="nav-item">
                            <a id="nav-link-tab2" class="nav-link" href="#" onclick="setTab(event, 2);"><span class="nav-link-tab">{{trans_choice('ui.Translation', 1)}}</span></a>
                        </li>
                        <li class="nav-item">
                            <a id="nav-link-tab3" class="nav-link" href="#" onclick="setTab(event, 3); ajaxexec('/articles/flashcards/view/{{$record->id}}', 'tab-tab3')"><span class="nav-link-tab">{{trans_choice('ui.Show All', 1)}}</span></a>
                        </li>
                        <li class="nav-item">
                            <a id="nav-link-tab4" class="nav-link" href="#" onclick="setTab(event, 4);"><span class="nav-link-tab">{{trans_choice('ui.Setting', 2)}}</span></a>
                        </li>
                    </ul>

                    <div id="tab-tab1" style="clear:both; display:default;">
                        <ul class="nav">
                            <li class="ml-2"><a id="flash1" onclick="clipboardCopy(event, 'flash1', 'description', false)" href="" tabindex="-1" class="small-thin-text">@LANG('ui.Copy')</a></li>
                            <li class="ml-2"><a onclick="event.preventDefault(); $('#description').val(''); pasteText('#description');" href="" tabindex="-1" class="small-thin-text">@LANG('ui.Paste')</a></li>
                            <li class="ml-2"><a id="flash1a" onclick="event.preventDefault(); $('#description').val(getSentences($('#description').val())); $('#flash1a').css('color', 'red');" href="" tabindex="-1" class="small-thin-text">@LANG('proj.Split Sentences')</a></li>
                            <li class="ml-2"><a href="" onclick="event.preventDefault(); $('#description').val(''); $('#description').focus();" class="small-thin-text ml-1">@LANG('ui.Clear')<a/></li>
                            <li class="ml-2"><a href="" onclick="event.preventDefault(); $('#description').val(''); $('#description_translation').val(''); $('#description').focus();" class="small-thin-text ml-1">@LANG('ui.Clear All')<a/></li>
                            <li class="ml-2"><a href="" onclick="event.preventDefault(); swap('description', 'description_translation');" class="small-thin-text ml-1">@LANG('ui.Swap')<a/></li>
                            <li class="ml-2"><a href="" onclick="event.preventDefault(); fixCase('description');" class="small-thin-text ml-1">@LANG('proj.Fix Case')<a/></li>
                        </ul>
                        <textarea rows="20" name="description" id="description" class="form-control big-text">{{$record->description}}</textarea>
                        <textarea rows="20" name="sentences" id="sentences" class="form-control big-text hidden">{{$sentences}}</textarea>
                    </div>
                    <div id="tab-tab2" style="clear:both; display:none;">
                        <ul class="nav">
                            <li class="ml-2"><a id="flash2" onclick="clipboardCopy(event, 'flash2', 'description_translation', false)" href="" tabindex="-1" class="small-thin-text">@LANG('ui.Copy')</a></li>
                            <li class="ml-2"><a onclick="event.preventDefault(); $('#description_translation').val(''); pasteText('#description_translation');" href="" tabindex="-1" class="small-thin-text">@LANG('ui.Paste')</a></li>
                            <li class="ml-2"><a id="flash2a" onclick="event.preventDefault(); $('#description_translation').val($('#sentences_translation').val()); $('#flash2a').css('color', 'red');" href="" tabindex="-1" class="small-thin-text">@LANG('proj.Split Sentences')</a></li>
                            <li class="ml-2"><a href="" onclick="event.preventDefault(); $('#description_translation').val(''); $('#description_translation').focus();" class="small-thin-text ml-1">@LANG('ui.Clear')<a/></li>
                            <li class="ml-2"><a href="" onclick="event.preventDefault(); $('#description').val(''); $('#description_translation').val(''); $('#description_translation').focus();" class="small-thin-text ml-1">@LANG('ui.Clear All')<a/></li>
                            <li class="ml-2"><a href="" onclick="event.preventDefault(); swap('description', 'description_translation');" class="small-thin-text ml-1">@LANG('ui.Swap')<a/></li>
                            <li class="ml-2"><a href="" onclick="event.preventDefault(); fixCase('description_translation');" class="small-thin-text ml-1">@LANG('proj.Fix Case')<a/></li>
                        </ul>
                        <textarea rows="20" name="description_translation" id="description_translation" class="form-control big-text">{{$record->description_translation}}</textarea>
                        <textarea rows="20" name="sentences_translation" id="sentences_translation" class="form-control big-text hidden">{{$sentences_translation}}</textarea>
                    </div>
                    <div id="tab-tab3" class="pt-2" style="clear:both; display:none; min-height:500px; overflow-y:auto;">
                        <!-- List of flashcards - will be replaced by ajax on every click -->
                    </div>
                    <div id="tab-tab4" class="pt-2" style="clear:both; display:none;">
                        <div class="entry-title-div mb-3">
                            <label class="tiny">@LANG('proj.Source'):</label>
                            <input type="text" id="source" name="source" placeholder="Source" class="form-control" onfocus="setFocus($(this), '#accent-chars')" value="{{$record->source}}" />
                        </div>

                        <div class="entry-title-div mb-3">
                            <label class="tiny">@LANG('ui.Author'):</label>
                            <input type="text" id="source_credit" name="source_credit" placeholder="Author" class="form-control" onfocus="setFocus($(this), '#accent-chars')" value="{{$record->source_credit}}" />
                        </div>

                        <div class="entry-title-div mb-3">
                            <label class="tiny">@LANG('proj.Source Link'):</label>
                            <input type="text" id="source_link" name="source_link" placeholder="Source Link" class="form-control" value="{{$record->source_link}}" />
                        </div>

                        <div class="entry-description-div mb-3">
                            <label class="tiny">@LANG('ui.Summary'):</label>
                            <textarea id="description_short" name="description_short" class="form-control entry-description-text" onfocus="setFocus($(this), '#accent-chars')" >{{ $record->description_short }}</textarea>
                        </div>
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

                @if (false)
                <!-- moved to checkboxes -->
                <div class="form-group">
                    <label for="options" class="control-label">{{trans_choice('ui.Option', 2)}}:</label>
                    <input type="text" name="options" class="form-control" value="{{$record->options}}" />
                    <p class='medium-thin-text'>Options: read-random;read-reverse;</p>
                </div>
                @endif

			</div>

			<div class="mb-3">
				<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
			</div>

			{{ csrf_field() }}
		</div>
	</form>

</div>

@endsection
