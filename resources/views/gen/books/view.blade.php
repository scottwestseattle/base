@php
$prefix = 'books';
$showPrevNext = (isset($prev) || isset($next));
$bookId = isset($options['book']) ? $options['book']->id : null;
@endphp
@extends('layouts.app')
@section('title', $options['page_title'] )
@section('menu-submenu')@component('gen.' . $prefix . '.menu-submenu', ['record' => $record, 'index' => 'index', 'bookId' => $bookId])@endcomponent @endsection
@section('content')

    <!------------------------------------>
    <!-- Top Navigation Buttons -->
    <!------------------------------------>
    @if ($showPrevNext)
    <div>
        @if (isset($prev))
            <a href="/{{$prefix}}/show/{{$prev->permalink}}"><button type="button" class="btn btn-success mb-1"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>@LANG('ui.Prev')</button></button></a>
        @endif
            <a href="{{$options['backLink']}}"><button type="button" class="btn btn-success mb-1"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-up"></span>{{trans_choice('proj.Back to Book', 1)}}</button></button></a>
        @if (isset($next))
            <a href="/{{$prefix}}/show/{{$next->permalink}}"><button type="button" class="btn btn-success mb-1">@LANG('ui.Next')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
        @endif
    </div>
    @endif

    <!------------------------------------>
    <!-- The Entry						-->
    <!------------------------------------>

    <div>
        <!-- Stats -->
        <div class="vertical-align">
            <div class="mb-2">
                <a type="button" class="btn btn-primary" href="/books/read/{{$record->id}}" >{{__('proj.Start Reading')}}<span style="font-size:16px;" class="glyphicon glyphicon-volume-up white ml-2"></span></a>
            </div>
            <div class="small-text">
                <div style="margin-right:15px; float:left;">{{$record->view_count}} {{trans_choice('ui.view', 2)}}</div>
                <div style="margin-right:15px; float:left;"><a href="/entries/stats/{{$record->id}}">{{$options['wordCount']}} {{trans_choice('ui.Word', 2)}}</a></div>
                @if (isAdmin())
                <span style="margin-left:10px;">
                    @component('components.control-button-publish', ['record' => $record, 'prefix' => 'books', 'showPublic' => true, 'ajax' => true, 'reload' => true])@endcomponent
                </span>
                @endif
                @if (isset($record->definitions) && count($record->definitions) > 0)
                    <div class="mr-2 float-left">
                        <a href="/entries/vocabulary/{{$record->id}}" class="btn btn-xs btn-primary" role="button">
                            <div class="middle mr-0" style="margin-bottom:2px;">Vocabulary</div>
                            <div class="badge badge-small badge-white middle ml-0">{{count($record->definitions)}}</div>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div style="clear: both;" class="mt-2">

            <!-- Title -->
            <div class="large-thin-text">{{$record->source}}</div>
            <h1 name="title">{{$record->title}}</h1>

            <!-- Summary -->
            @if (strlen(trim($record->description_short)) > 0)
                <div class="entry" style="margin-bottom:20px; font-size:1.3em;">
                    <div><i>{{$record->description_short}}</i></div>
                </div>
            @endif

            <div class="entry-div" style="margin-top:20px; width:100%; font-size:1.1em;">
                <div class="entry" style="width:100%;">
                    <span name="description" class="">{!! $record->description !!}</span>
                </div>
            </div>

        </div>

        <div class="mt-4 small-thin-text">
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
    @if ($showPrevNext)
    <div style="margin-top: 10px;">
        @if (isset($prev))
            <a href="/{{$prefix}}/show/{{$prev->permalink}}"><button type="button" class="btn btn-success"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>@LANG('ui.Prev')</button></button></a>
        @endif
        @if (isset($next))
            <a href="/{{$prefix}}/show/{{$next->permalink}}"><button type="button" class="btn btn-success">@LANG('ui.Next')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
        @endif
    </div>
    @endif


@endsection
