@extends('layouts.app')
@section('title', __('proj.Convert Text to Favorites'))
@section('content')
@php
    $translation = isset($parms['translation']) ? $parms['translation'] : null;
@endphp
<div class="container page-normal">

    <h1>{{__('proj.Convert Text to Favorites')}}</h1>

	<form method="POST" action="/definitions/convert-text-to-favorites/{{ $record->id }}">

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

        <input type="hidden" id="language_flag" value="{{$record->language_flag}}" />

	{{ csrf_field() }}
	</form>
</div>
@endsection
