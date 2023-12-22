@php
    $showGlobalSearchBox = false;
    $locale = app()->getLocale();
@endphp
@extends('layouts.app')
@section('title', __('proj.Dictionary Search'))
@section('menu-submenu')@component('gen.definitions.menu-submenu', ['locale' => $locale])@endcomponent @endsection
@section('content')

	<h1>@LANG('proj.Dictionary')<span class="title-count" id="searchDefinitionsResultsCount">({{count($records)}})</span>
		<span style="" class="small-thin-text mb-2">
			<a href="{{route('dictionary.search', ['sort' => 1, 'locale' => $locale])}}">A-Z</a>
			<a class="ml-2" href="{{route('dictionary.search', ['sort' => 2, 'locale' => $locale])}}">Z-A</a>
			<a class="ml-2" href="{{route('dictionary.search', ['sort' => 14, 'locale' => $locale])}}">{{__('proj.Ranked')}}</a>
			<a class="ml-2" href="{{route('dictionary.search', ['sort' => 9, 'locale' => $locale])}}">{{trans_choice('proj.Verb', 2)}}</a>
			<a class="ml-2" href="{{route('dictionary.search', ['sort' => 15, 'locale' => $locale])}}">{{__('proj.Verbs (Ranked)')}}</a>
			<a class="ml-2" href="{{route('dictionary.search', ['sort' => 3, 'locale' => $locale])}}">{{__('proj.Newest')}}</a>
			<a class="ml-2" href="{{route('dictionary.search', ['sort' => 31, 'locale' => $locale])}}">{{__('proj.Oldest')}}</a>
			<a class="ml-2" href="{{route('dictionary.search', ['sort' => 4, 'locale' => $locale])}}">{{__('proj.Recent')}}</a>
			<a class="ml-2" href="{{route('dictionary.search', ['sort' => 10, 'locale' => $locale])}}">{{__('proj.All')}}</a>
			@if (isAdmin())
				<a class="ml-2" href="{{route('dictionary.search', ['sort' => 8, 'locale' => $locale])}}">{{'not finished'}}</a>
				<a class="ml-2" href="{{route('dictionary.search', ['sort' => 5, 'locale' => $locale])}}">{{'missing translation'}}</a>
				<a class="ml-2" href="{{route('dictionary.search', ['sort' => 6, 'locale' => $locale])}}">{{'missing definition'}}</a>
				<a class="ml-2" href="{{route('dictionary.search', ['sort' => 7, 'locale' => $locale])}}">{{'missing conjugation'}}</a>
			@endif
		</span>
	</h1>

	<div class="mb-3">
		<form method="POST" action="{{route('dictionary.createQuick', ['locale' => $locale])}}">
			<input type="text" id="title" name="title" value="{{$search}}" class="form-control" autocomplete="off"
			onfocus="$(this).select(); setFocus($(this));"
            oninput="showSearchResult(this.value, {{SEARCHTYPE_DEFINITIONS}}, 'title', 'searchResults');"
			autofocus />
		</form>
	</div>

	<div id="searchResults" class="row">

		@component('gen.definitions.component-search-results', [
		    'records' => $records, 'favoriteLists' => $favoriteLists])
		@endcomponent

	</div>

@endsection

