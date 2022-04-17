@php
    $title = isset($record->title) ? $record->title : __('proj.not found');
    $prefix = 'gen.definitions';
@endphp
@extends('layouts.app')
@section('title', trans_choice('proj.View Definition', 1) . ' - ' . $title)
@section('menu-submenu')@component('gen.definitions.menu-submenu', ['prefix' => 'definitions'])@endcomponent @endsection
@section('content')

	@if (isset($fromDictionary))
	<div class="page-nav-buttons">
		<a class="btn btn-success btn-sm btn-nav-top" role="button" href="/{{$prefix}}/">
		    @LANG('content.Back to Dictionary')<span class="glyphicon glyphicon-button-back-to"></span>
		</a>
	</div>
	@endif

	<!-- Top nav buttons -->
	@if (isset($prev) || isset($next))
	<div class="page-nav-buttons">
		<a class="btn btn-primary btn-sm btn-nav-lesson {{isset($prev) ? '' : 'disabled'}}" role="button" href="/{{$prefix}}/view/{{isset($prev) ? $prev->id : 0}}">
			<span class="glyphicon glyphicon-button-prev"></span>
			@LANG('ui.Prev')
		</a>
		<a class="btn btn-primary btn-sm btn-nav-lesson {{isset($next) ? '' : 'disabled'}}" role="button" href="/{{$prefix}}/view/{{isset($next) ? $next->id : 0}}">
			@LANG('ui.Next')
			<span class="glyphicon glyphicon-button-next"></span>
		</a>
	</div>
	@endif

	<!-- Show the record -->
	@if (isset($record))

    <table><tr>
        @if (Auth::check())
        <td class="icon">
	    	@component($prefix . '.component-search-toolbar', ['record' => $record, 'id' => 1, 'lists' => $favoriteLists])@endcomponent
        </td>
        <td class="icon">
            <div class="ml-3">
                @if (isAdmin() || App\User::isOwner($record->user_id))
                    <a href="/definitions/edit/{{$record->id}}">@component('components.icon-edit')@endcomponent</a>
                @endif
            </div>
        </td>
        @endif
    </tr></table>

	<div>
		<h3>
 			<div class="middle">
			    @if ($record->isSnippet())
                    {{$record->title}}
			    @else
                    <div class="float-left">
                        {{$record->title}}
                        @component('components.badge', ['text' => $record->view_count . ' ' . trans_choice('ui.view', 2)])@endcomponent
                    </div>
                    <div class="ml-3 small-thin-text middle"><a target='_blank' href="https://dle.rae.es/{{$record->title}}">RAE</a></div>
                    @if (isAdmin())
                    <div class="ml-2 small-thin-text middle"><a target='_blank' href="https://www.spanishdict.com/translate/{{$record->title}}">SpanishDict</a></div>
                    @endif
                    <div class="small-thin-text">{{__(strtolower($record->getPos()))}}</div>
                @endif
			</div>

			@if (App\User::isSuperAdmin())
				@if (isset($canConjugate) && $canConjugate)
					<div class="small-thin-text mt-2"><a href="/{{PREFIX . '/conjugationsgen/' . $record->id}}/">generate conjugations</a>
				@endif
				@if (App\Gen\Spanish::fixConjugations($record))
					<div class="small-thin-text mt-2"><a href="/{{PREFIX . '/edit/' . $record->id}}/">fix conjugation</a>
				@endif
			@endif

		</h3>
	</div>

	<div class="">
		@if (isset($record->definition))
			<p style="font-size:1.2em;">{!! nl2br($record->definition) !!}</p>
		@endif
		@if (isset($record->translation_en))
			<p style="font-size:1.2em;">{{$record->translation_en}}</p>
		@endif
		@if (isset($record->examples))
            @foreach($record->examples as $example)
                <p><i>{{$example}}</i></p>
            @endforeach
		@endif
	<div>
	@else

	<div style="mt-3">
		<h3>{{$word}}</h3>
	</div>

	<div class="">
		<p style="font-size:1.2em;">{{__('proj.Not found in dictionary')}}</p>
		<p><a target='_blank' href="/definitions/add/{{$word}}">{{__('ui.Add')}}</a></p>
		<p><a target='_blank' href="https://dle.rae.es/{{$word}}">Real Academia Española: {{$word}}</a></p>
		<p><a target='_blank' href="https://translate.google.com/#view=home&op=translate&sl=es&tl=en&text={{$word}}">Google Translate: {{$word}}</a></p>
		<p><a target='_blank' href="https://www.spanishdict.com/translate/{{$word}}">Span¡shD!ct.com: {{$word}}</a></p>
	</div>

	@endif

	<!-- Bottom nav buttons -->
	<div class="page-nav-buttons">
		@if (isset($prev))
		<a class="btn btn-primary btn-sm btn-nav-lesson" role="button" href="/{{$prefix}}/view/{{$prev->id}}">
			<span class="glyphicon glyphicon-button-prev"></span>
			{{$prev->title}}
		</a>
		@endif
		@if (isset($next))
		<a class="btn btn-primary btn-sm btn-nav-lesson" role="button" href="/{{$prefix}}/view/{{$next->id}}">
			{{$next->title}}
			<span class="glyphicon glyphicon-button-next"></span>
		</a>
		@endif
	</div>

	@if (isset($record->forms) && strlen($record->forms) > 0)
		<div class="large-thin-text mt-2 mb-1">{{__('proj.Forms')}}</div>
		<div class="medium-thin-text mt-2 mb-4">{{App\Gen\Spanish::getFormsPretty($record->forms)}}</div>
	@endif

    @if ($record->isConjugated())
    	@component($prefix . '.component-conjugations-full', ['record' => $record])@endcomponent
    @endif
@endsection

