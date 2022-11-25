@php
    $translation = isset($options['translation']) ? $options['translation'] : null;
@endphp
@extends('layouts.app')
@section('title', $options['page_title'] )
@section('menu-submenu')@component('gen.articles.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
    <!------------------------------------>
    <!-- Top Navigation Buttons -->
    <!------------------------------------>

    @if (false)

    @if (isset($prev) || isset($record->parent_id) || isset($next))
    <div style="margin-top: 10px;">
        @if (isset($prev))
            <a href="/entries/{{$prev->permalink}}"><button type="button" class="btn btn-blog-nav"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>@LANG('ui.Prev')</button></button></a>
        @endif

        <a href="{{$backLink}}"><button type="button" class="btn btn-blog-nav">@LANG($backLinkText)<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>

        @if (isset($next))
            <a href="/entries/{{$next->permalink}}"><button type="button" class="btn btn-blog-nav">@LANG('ui.Next')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
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
    <!-- The Entry						-->
    <!------------------------------------>

    <div>
        <!-- Stats -->
        <div class="mb-2">
            <div class="mb-2">
                <a type="button" class="btn btn-primary" href="/articles/read/{{$record->id}}" >{{__('proj.Start Reading')}}<span style="font-size:14px;" class="glyphicon glyphicon-volume-up white ml-2"></span></a>
                @if ($options['lineCount'] > 25)
                    <a type="button" class="btn btn-primary mt-1" href="/articles/read/{{$record->id}}?count=20&random=1" ><span style="font-size:.9em;">{{__('proj.Start Reading')}}</span><span class="title-count">(20)</span><span style="font-size:14px;" class="glyphicon glyphicon-volume-up white ml-2"></span></a>
                @endif
                @if ($record->hasTranslation())
				    <a href="/articles/flashcards/{{$record->id}}"><button class="btn btn-success mt-1">@LANG('proj.Flashcards') <span class="title-count">({{$options['lineCount']}})</span></button></a>
                    @if ($options['lineCount'] > 25)
                        <a type="button" class="btn btn-success mt-1" href="/articles/flashcards/{{$record->id}}?count=20&random=1" >{{__('proj.Flashcards')}}<span class="title-count">(20)</span></a>
                    @endif
				@endif
            </div>

            <div class="small-text">
                <div style="margin-right:10px; float:left;">{{App\DateTimeEx::getShortDateTime($record->display_date, 'M d, Y')}}</div>
                <div style="margin-right:10px; float:left;">{{$record->view_count}} {{trans_choice('ui.view', 2)}}</div>
                <div style="margin-right:10px; float:left;"><a href="/entries/stats/{{$record->id}}">{{$options['lineCount']}} {{trans_choice('ui.Line', 2)}}</a></div>
                <div style="margin-right:10px; float:left;">{{$options['letterCount']}} {{trans_choice('ui.Letter', 2)}}</div>
                <span style="margin-left:10px;">
                    @component('components.control-button-publish', ['record' => $record, 'prefix' => 'articles', 'showPublic' => true,  'ajax' => true, 'reload' => true])@endcomponent
                </span>
                @if (isset($record->definitions) && count($record->definitions) > 0)
                    <div class="mr-2 float-left">
                        <a href="/entries/vocabulary/{{$record->id}}" class="btn btn-xs btn-primary" role="button">
                            <div class="middle mr-0" style="margin-bottom:2px;">Vocabulary</div>
                            <div class="badge badge-small badge-white middle ml-0">{{count($record->definitions)}}</div>
                        </a>
                    </div>
                @endif
                @if (isset($translation))
                    <div class="mr-2 float-left">
                        <a href="" onclick="event.preventDefault(); $('#description').toggle(); $('#translation').toggle(); " class="btn btn-xs btn-success" role="button">
                            <div class="middle mr-0" style="">{{trans_choice('ui.Translation', 2)}}</div>
                        </a>
                    </div>
                    <div class="mr-2 float-left">
                        <a href="/definitions/convert-text-to-favorites/{{$record->id}}" class="btn btn-xs btn-primary" role="button">
                            <div class="middle mr-0" style="">{{trans_choice('proj.Convert to Favorites', 2)}}</div>
                        </a>
                    </div>
                @endif
                @if (isset($options['translation_matches']) && !$options['translation_matches'])
                    <div class="red" style="clear:both;">TRANSLATION DOES NOT MATCH TEXT</div>
                @endif
            </div>
        </div>

        <div style="clear: both;" class="">

            <!-- Title -->
            <h1 name="title">{{$record->title}}</h1>

            <!-- Summary -->
            @if (strlen(trim($record->description_short)) > 0)
                <div class="entry" style="margin-bottom:20px; font-size:1.3em;">
                    <div><i>{{$record->description_short}}</i></div>
                </div>
            @endif

            <div class="entry-div" style="margin-top:20px; width:100%; font-size:1.1em;">
                <div class="entry" style="width:100%;">
                    <span id="description" name="description" class="">{!! $record->description !!}</span>
                    @if (isset($translation))
                        <span id="translation" name="translation" class="hidden">
                            @component('shared.flashcards-view', ['records' => $translation])@endcomponent
                        </span>
                    @endif
                </div>
            </div>

        </div>

        <div class="mt-4 small-thin-text">
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

	<!------------------------------------>
	<!-- Bottom Navigation Buttons -->
	<!------------------------------------>

	@if (false)

	<div class="trim-text" style="max-width:100%; margin-top: 30px;">
		@if (isset($prev))
			<div class="" style="float:left; margin: 0 5px 5px 0;" >
				<a href="/entries/{{$prev->permalink}}"><button type="button" class="btn btn-nav-bottom"><span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-left"></span>{{$prev->title}}</button></a>
			</div>
		@endif
		@if (isset($next))
			<div style="float:left;">
				<a href="/entries/{{$next->permalink}}"><button type="button" class="btn btn-nav-bottom"><span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-right"></span>{{$next->title}}</button></a>
			</div>
		@endif
	</div>

	@endif

@endsection
