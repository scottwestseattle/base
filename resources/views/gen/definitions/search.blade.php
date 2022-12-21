@php
    $showGlobalSearchBox = false;
@endphp
@extends('layouts.app')
@section('title', __('proj.Dictionary Search'))
@section('menu-submenu')@component('gen.definitions.menu-submenu')@endcomponent @endsection
@section('content')

	<h1>@LANG('proj.Dictionary') (<span id="searchDefinitionsResultsCount">{{count($records)}}</span>)
		<span style="" class="small-thin-text mb-2">
			<a href="/dictionary/search/1">A-Z</a>
			<a class="ml-2" href="/dictionary/search/2">Z-A</a>
			<a class="ml-2" href="/dictionary/search/14">{{__('proj.Most Common')}}</a>
			<a class="ml-2" href="/dictionary/search/9">{{trans_choice('proj.Verb', 2)}}</a>
			<a class="ml-2" href="/dictionary/search/15">{{__('proj.Verbs (most common)')}}</a>
			<a class="ml-2" href="/dictionary/search/3">{{__('proj.Newest')}}</a>
			<a class="ml-2" href="/dictionary/search/4">{{__('proj.Recent')}}</a>
			<a class="ml-2" href="/dictionary/search/10">{{__('proj.All')}}</a>
			@if (isAdmin())
				<a class="ml-2" href="/dictionary/search/8">{{'not finished'}}</a>
				<a class="ml-2" href="/dictionary/search/5">{{'missing translation'}}</a>
				<a class="ml-2" href="/dictionary/search/6">{{'missing definition'}}</a>
				<a class="ml-2" href="/dictionary/search/7">{{'missing conjugation'}}</a>
			@endif
		</span>
	</h1>

	<div class="mb-3">
		<form method="POST" action="/dictionary/create">
			<input type="text" id="title" name="title" value="{{$search}}" class="form-control" autocomplete="off"
			onfocus="$(this).select(); setFocus($(this));"
            oninput="showSearchResult(this.value, false, 'searchResults');"
			autofocus />
		</form>
	</div>

	<div id="searchResults" class="row">

		@component('gen.definitions.component-search-results', [
		    'records' => $records, 'favoriteLists' => $favoriteLists])
		@endcomponent

	</div>

@endsection

