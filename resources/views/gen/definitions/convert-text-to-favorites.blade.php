@php
    $translation = isset($parms['translation']) ? $parms['translation'] : null;
    $locale = app()->getLocale();
    $showResults = isset($parms['urlList']);
@endphp
@extends('layouts.app')
@section('title', __('proj.Convert Text to Favorites'))
@section('menu-submenu')@component('gen.articles.menu-submenu', ['locale' => $locale, 'record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

    <h1>{{__('proj.Convert Text to Favorites')}}</h1>

	<form method="POST" action="{{route('definitions.convertTextToFavoritesPost', ['locale' => $locale, 'entry' => $record->id])}}">

        @if ($showResults)
    		<h4>Article <a href="{{$parms['urlArticle']}}">{{$record->title}}</a> converted to <a href="{{$parms['urlList']}}">Snippets</a></h4>
            <div class="mt-3"><a href="{{$parms['urlList']}}">Show New Snippets</a></div>
            <div class="mt-3"><a href="{{$parms['urlArticle']}}">Edit Article</a></div>
        @else
    		<h4>{{$record->title}}</h4>
            <div class="entry-title-div mb-3">
                <label class="tiny">@LANG('ui.Favorites List Name'):</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ $record->title }}" placeholder="Favorites List Name" />
            </div>

            <div class="submit-button">
                <button type="submit" class="btn btn-primary">@LANG('ui.Convert')</button>
            </div>

            <div class="entry-div" style="margin-top:20px; width:100%; font-size:1.1em;">
                <div class="entry" style="width:100%;">
                    @if (isset($translation))
                        <span id="translation" name="translation" class="">
                            @component('shared.flashcards-view', ['records' => $translation])@endcomponent
                        </span>
                    @endif
                </div>
            </div>

            <div class="submit-button">
                <button type="submit" class="btn btn-primary">@LANG('ui.Convert')</button>
            </div>
		@endif

        <input type="hidden" id="language_flag" value="{{$record->language_flag}}" />

	{{ csrf_field() }}
	</form>
</div>
@endsection
