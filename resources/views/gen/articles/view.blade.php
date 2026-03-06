@php
    $locale = app()->getLocale();
    $translation = isset($options['sentences_translation']) ? $options['sentences_translation'] : null;
    $translationMatches = true;
    if (isset($options['translation_matches']) && !$options['translation_matches'])
    {
        $translationMatches = false;
        $cntSentences = count($options['sentences']);
        $cntTranslations = count($options['sentences_translation']);
    }

    // quizes
    $qnaPorPara = isset($options['qnaPorPara']) ? $options['qnaPorPara']['count'] : 0;
    $qnaEraFue = isset($options['qnaEraFue']) ? $options['qnaEraFue']['count'] : 0;

    // get/set view mode: paragraph, sentences, or side-by-side
    $setSessionUrl = '/set-session?tag=articlesViewMode&value=';
    $checked1 = $checked2 = $checked3 = '';
    $hidden1 = $hidden2 = $hidden3 = 'hidden';

    if (isset($translation))
    {
        $viewMode = session('articlesViewMode');
        if ($viewMode == '2')
        {
            $checked2 = 'checked';
            $hidden2 = ''; // not hidden
        }
        elseif ($viewMode == '3')
        {
            $checked3 = 'checked';
            $hidden3 = ''; // not hidden
        }
        else
        {
            if (isset($translation))
            {
                // default to Sentences
                $checked1 = 'checked';
                $hidden1 = ''; // not hidden
            }
            else
            {
                // default to Paragraphs
                $checked3 = 'checked';
                $hidden3 = ''; // not hidden
            }
        }
    }
    else
    {
        // default to Paragraphs
        $checked3 = 'checked';
        $hidden3 = ''; // not hidden
    }

    $coverImage = \App\Gen\Article::getCoverImage($record->id, $record->level_flag);
    $hasExercises = $record->hasTranslation() || $qnaPorPara || $qnaEraFue;
@endphp
@extends('layouts.app')
@section('title', $options['page_title'] )
@section('menu-submenu')@component('gen.articles.menu-submenu', ['locale' => $locale, 'record' => $record])@endcomponent @endsection
@section('content')
    <!------------------------------------>
    <!-- Top Navigation Buttons -->
    <!------------------------------------>

    @if (false)

    @if (isset($prev) || isset($record->parent_id) || isset($next))
    <div style="margin-top: 10px;">
        @if (isset($prev))
            <a href="{{route('entries.permalink', ['locale' => $locale, 'permalink' => $record->permalink])}}"><button type="button" class="btn btn-blog-nav"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>@LANG('ui.Prev')</button></button></a>
        @endif

        <a href="{{$backLink}}"><button type="button" class="btn btn-blog-nav">@LANG($backLinkText)<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>

        @if (isset($next))
            <a href="{{route('entries.permalink', ['locale' => $locale, 'permalink' => $next->permalink])}}"><button type="button" class="btn btn-blog-nav">@LANG('ui.Next')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
        @endif

    </div>
    @elseif (isset($backLink) && isset($backLinkText) && !((Auth::user() && (Auth::user()->user_type >= 1000))))
    <div style="margin-top: 10px;">
        <a href="{{$backLink}}">
            <button type="button" class="btn btn-blog-nav">{{$backLinkText}}
                <span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span>
            </button>
        </a>
    </div>
    @endif

    @endif

    <!------------------------------------>
    <!-- Top Buttons and Controls		-->
    <!------------------------------------>

    <div>
        <div class="mb-1">
            @if (isset($coverImage))
                <div class="image-wrapper mb-2">
                    <img src="{{$coverImage['url']}}" style="width:90%; max-width: 333px" />
                    <span class="image-lable" style="background: {{$coverImage['lableColor']}};">{{$coverImage['lable']}}</span>
                </div>
            @else
                <h1 class="large-thin-text" style="margin-top: 0px; margin-bottom: 2px; font-size: 2em;">{{$record->title}}</h1>
            @endif

            <div class="small-text">
                <!-- Stats -->
                <!-- div style="margin-right:10px; float:left;">{{App\DateTimeEx::getShortDateTime($record->display_date, 'M d, Y', false)}}</div -->
                <div style="margin-right:10px; float:left;"><a href="{{route('entries.stats', ['locale' => $locale, 'entry' => $record->id])}}">{{$options['lineCount']}} {{trans_choice('ui.Line', 2)}}</a></div>
                <div style="margin-right:10px; float:left;">{{$record->view_count}} {{trans_choice('ui.view', 2)}}</div>

                @if (isMember())
                    <div class="" style="clear:both;"></div>
                @endif

                @if ($hasExercises)
                    <div style="margin-right:10px; float:left;"><a type="button" class="btn btn-primary btn-xs" href="#practice-exercises" >{{trans_choice('ui.Exercise', 2)}}<span style="" class="glyphicon glyphicon-education white ml-1"></span></a></div>
                @endif
                <div style="margin-right:10px; float:left;"><a type="button" class="btn btn-primary btn-xs" href="{{route('articles.read', ['locale' => $locale, 'entry' => $record->id])}}" >{{__('ui.Read')}}<span style="" class="glyphicon glyphicon-volume-up white ml-1"></span></a></div>

                <span style="margin-left:10px;">
                    @component('components.control-button-publish', ['record' => $record, 'prefix' => 'articles', 'showPublic' => true,  'ajax' => true, 'reload' => true])@endcomponent
                </span>
                @if (false && isset($record->definitions) && count($record->definitions) > 0)
                    <div class="mr-2 float-left">
                        <a href="{{route('entries.vocabulary', ['locale' => $locale, 'entry' => $record->id])}}" class="btn btn-xs btn-primary" role="button">
                            <div class="middle mr-0" style="margin-bottom:2px;">Vocabulary</div>
                            <div class="badge badge-small badge-white middle ml-0">{{count($record->definitions)}}</div>
                        </a>
                    </div>
                @endif

                @if (isset($translation))

                    <!-- Convert text to Snippets -->
                    @if (isMember())
                        <div class="mr-2 float-left">
                            <a href="{{route('definitions.convertTextToFavorites', ['locale' => $locale, 'entry' => $record->id])}}" class="btn btn-xs btn-primary" role="button">
                                <div class="middle mr-0" style="">{{trans_choice('proj.Convert to Favorites', 2)}}</div>
                            </a>
                        </div>
                    @endif

                    @if (false)
                        <a href="{{route('definitions.convertQuestionsToSnippets', ['locale' => $locale, 'entry' => $record->id])}}" class="btn btn-xs btn-primary" role="button">
                            <div class="middle mr-0" style="">{{__('proj.Convert Questions to Snippets')}}</div>
                        </a>
                    @endif

                    @if (!$translationMatches)
                        <div class="red" style="clear:both;">TRANSLATION DOES NOT MATCH TEXT ({{$cntSentences}}<>{{$cntTranslations}})</div>
                    @endif

                    <!-- Read Style Options: Sentence, Side x Side, Normal (Paragraph) -->
                    <div class="form-group mt-2" style="clear:both;">
                        <div class="radio-group-item float-left mr-3">
                            <label>
                            <input type="radio" name="radio_sample" value="1" class="form-control-inline" onclick="$('#description').hide(); $('#sentence-view').show(); $('#side-by-side').hide(); ajaxexec('{{$setSessionUrl . 1}}');" {{$checked1}}>
                            {{trans_choice('ui.Sentence', 2)}}
                            </label>
                        </div>
                        <div class="radio-group-item float-left mr-3">
                            <label>
                            <input type="radio" name="radio_sample" value="2" class="form-control-inline" onclick="$('#description').hide(); $('#sentence-view').hide(); $('#side-by-side').show(); ajaxexec('{{$setSessionUrl . 2}}');" {{$checked2}}>
                            {{__('ui.Side by Side')}}
                            </label>
                        </div>
                        <div class="radio-group-item float-left mr-3">
                            <label>
                            <input type="radio" name="radio_sample" value="3" class="form-control-inline"  onclick="$('#description').show(); $('#sentence-view').hide(); $('#side-by-side').hide(); ajaxexec('{{$setSessionUrl . 3}}');" {{$checked3}}>
                            {{__('ui.Normal')}}
                            </label>
                        </div>
                        @if (false)
                        <div class="radio-group-item float-left mr-3">
                            <label>
                            <input type="radio" name="radio_sample" value="3" class="form-control-inline"  onclick="$('#description').show(); $('#sentence-view').hide(); $('#side-by-side').hide(); ajaxexec('{{$setSessionUrl . 3}}');" {{$checked3}}>
                            {{__('ui.Vocabulary')}}
                            </label>
                        </div>
                        @endif

                    </div>
                @endif
            </div>
        </div>

    <!------------------------------------>
    <!-- The Entry						-->
    <!------------------------------------>

        <div style="clear: both;" class="">

            @if (!$record->isStory())
            <div style="">
                <!-- Summary -->
                @if (strlen(trim($record->description_short)) > 0)
                    <div class="entry" style="margin-bottom:20px; font-size:1.3em;">
                        <div><i>{{$record->description_short}}</i></div>
                    </div>
                @endif
            </div>
            @endif
            <div class="entry-div" style="width:100%; font-size:1.1em;">
                <div class="entry" style="width:100%;">
                    @if (isset($translation))
                        <!------------------------------------>
                        <!-- Sentence View					-->
                        <!------------------------------------>
                        <span id="sentence-view" name="sentence-view" class="{{$hidden1}}">
                            <div style="font-size: .8em; color: green;"><i>{{__('proj.Click or tap sentences for translation')}}</i></div>
                            @foreach($options['sentences'] as $s)
                                @php
                                    $trx = isset($options['sentences_translation'][$loop->index]) ? $options['sentences_translation'][$loop->index] : null;
                                    $id = 'translation' . $loop->index;
                                @endphp
                                <div class="">
                                    <div class="mt-3"><a href="" onclick="event.preventDefault(); $('#{{$id}}').toggle()" style="text-decoration:none; color: black;">{{$s}}</a></div>
                                    <div id="{{$id}}" class="mt-1 mb-3  hidden" style="font-size:.9em;"><a style="color: #0451af" href="" onclick="event.preventDefault(); $('#{{$id}}').toggle()" style="text-decoration:none;">{{$trx}}</a></div>
                                </div>
                            @endforeach
                        </span>
                    @endif
                    @if (isset($translation))
                        <!------------------------------------>
                        <!-- Side-by-side View  			-->
                        <!------------------------------------>
                        <span id="side-by-side" name="side-by-side" class="{{$hidden2}}">
                            <table class="table table-striped table-borderless">
                                <tbody>
                                    @foreach($options['sentences'] as $s)
                                        <tr class="mb-3">
                                            @php
                                                $trx = isset($options['sentences_translation'][$loop->index]) ? $options['sentences_translation'][$loop->index] : null;
                                                $i = $loop->index + 1;
                                            @endphp
                                            <td class="pb-4 pr-4" style="vertical-align:top; width:50%;">{{$s}}</td>
                                            <td class="pb-4" style="vertical-align:top; color: #0451af">{{$trx}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </span>
                    @endif
                    <!------------------------------------>
                    <!-- Paragraph View       			-->
                    <!------------------------------------>
                    @php
                        $text = $record->getText();
                        $text = (!empty($text['text'])) ? $text['text'] : $text['trx'];
                        //dump($text);
                    @endphp
                    <span id="description" name="description" class="{{$hidden3}}">{!! $text !!}</span>
                </div>
            </div>

        </div>

        <div class="mt-2 small-thin-text"><a type="button" class="btn btn-primary btn-xs" href="#top" >{{__('base.Back to Top')}}<span class="glyphicon glyphicon-circle-arrow-up white ml-1"></span></a></div>

        <div class="mt-2 small-thin-text">
            @if (strlen($record->source) > 0)
                <div class="mb-2">{{$record->source}}</div>
            @endif

            @if (strlen($record->source_credit) > 0)
                 <div class="mb-2">{{__('ui.Author')}} {{$record->source_credit}}</div>
            @endif

            @if (strlen($record->source_link) > 0)
                <div class="mb-2"><a target="_blank" href="{{$record->source_link}}">{{$record->source_link}}</a></div>
            @endif
        <div>

    </div>

	<!----------------------------------------------->
	<!-- Quiz Options: Flashcards, Por v Para, etc -->
	<!----------------------------------------------->
	@if ($hasExercises)
    <div class="mb-1">
        <h1 id="practice-exercises" class="mt-2 large-thin-text" style="font-size: 2em;">{{__('proj.Practice Exercises')}}</h1>

        @if ($record->hasTranslation())
            <a href="{{route('articles.flashcards', ['locale' => $locale, 'entry' => $record->id])}}"><button class="btn btn-success mt-1">@LANG('proj.Flashcards') <span class="title-count">({{$options['lineCount']}})</span></button></a>
            @if ($options['lineCount'] > 25)
                <a type="button" class="btn btn-success mt-1" href="{{route('articles.flashcards', ['locale' => $locale, 'entry' => $record->id])}}?count=20&random=1" >{{__('proj.Flashcards')}}<span class="title-count">(20)</span></a>
            @endif
        @endif
        @if ($qnaPorPara)
            <a href="{{route('articles.quiz', ['locale' => $locale, 'entry' => $record->id, 'qnaType' => 'por'])}}"><button class="btn btn-success mt-1">@LANG('POR vs PARA') ({{$qnaPorPara}})</button></a>
        @endif
        @if ($qnaEraFue)
            <a href="{{route('articles.quiz', ['locale' => $locale, 'entry' => $record->id, 'qnaType' => 'era'])}}"><button class="btn btn-success mt-1">@LANG('Pretérito vs Imperfecto') ({{$qnaEraFue}})</button></a>
        @endif
    </div>
    @endif

	<!------------------------------------>
	<!-- Bottom Navigation Buttons -->
	<!------------------------------------>
	<div class="trim-text" style="max-width:100%; margin-top: 30px;">
		@if (isset($prev))
			<div class="" style="float:left; margin: 0 5px 5px 0;" >
				<a href="{{route('entries.permalink', ['locale' => $locale, 'entry' => $prev->permalink])}}"><button type="button" class="btn btn-nav-bottom"><span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-left"></span>{{$prev->title}}</button></a>
			</div>
		@endif
		@if (isset($next))
			<div style="float:left;">
				<a href="{{route('entries.permalink', ['locale' => $locale, 'entry' => $next->permalink])}}"><button type="button" class="btn btn-nav-bottom"><span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-right"></span>{{$next->title}}</button></a>
			</div>
		@endif
	</div>

@endsection
