@php
    $locale = app()->getLocale();
@endphp
@extends('layouts.app')
@section('title', __('proj.Add Article'))
@section('menu-submenu')@component('gen.articles.menu-submenu', ['locale' => $locale])@endcomponent @endsection
@section('content')
@php
    $type_flag = isAdmin() ? null : ENTRY_TYPE_ARTICLE;
@endphp

<div class="container page-normal">

	<h1>{{__('proj.Add Article')}}</h1>

	<form method="POST" action="{{route('articles.create', ['locale' => $locale])}}">
		<div class="form-control-big">

            @component('components.control-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent
			@component('components.control-accent-chars-esp', ['visible' => true, 'flat' => true])@endcomponent

			@if (isset($type_flag))
				<input type="hidden" name="type_flag" value="{{$type_flag}}">
			@else
				@component('components.control-entry-types', ['entryTypes' => App\Entry::getEntryTypes(), 'current_type' => 2])@endcomponent
			@endif

			@if (isset($parent_id))
				<input type="hidden" name="parent_id" value="{{$parent_id}}">
			@endif

			@if (isset($type_flag) && $type_flag == ENTRY_TYPE_LESSON)
			@else
			    @if (false)
				    @component('components.control-dropdown-date', ['div' => true,
				        'months' => $dates['months'],
				        'years' => $dates['years'],
				        'days' => $dates['days'],
				        'filter' => $filter
				    ])@endcomponent
                @endif
			@endif

            @if (isAdmin())
                <div class="entry-title-div mb-3 mt-2">
                    <input onblur="javascript:urlEncode('title', 'permalink')" type="text" id="title" name="title" placeholder="@LANG('ui.Title')" onfocus="setFocus($(this), '#accent-chars')" class="form-control" autofocus />
                </div>

                <div class="entry-title-div mb-3">
                    <input tabindex="-1" type="text" id="permalink" name="permalink" class="form-control"  placeholder="Permalink" />
                </div>
            @else
                <div class="entry-title-div mb-3 mt-2">
                    <input type="text" id="title" name="title" placeholder="@LANG('ui.Title')" onfocus="setFocus($(this), '#accent-chars')" class="form-control" autofocus />
                </div>
            @endif

			<div class="entry-title-div mb-3">
				<input type="text" id="source" name="source" placeholder="@LANG('proj.Source')" onfocus="setFocus($(this), '#accent-chars')" class="form-control" />
			</div>

			<div class="entry-title-div mb-3">
				<input type="text" id="source_credit" name="source_credit" placeholder="@LANG('ui.Author')" onfocus="setFocus($(this), '#accent-chars')" class="form-control" />
			</div>

                <div class="entry-title-div mb-3">
                    <input type="text" id="source_link" name="source_link" placeholder="@LANG('proj.Source Link')" class="form-control" />
                </div>

			<div class="entry-description-div mb-2">
				<textarea rows="3" id="description_short" name="description_short" class="form-control" placeholder="@LANG('ui.Summary')" onfocus="setFocus($(this), '#accent-chars')"></textarea>
			</div>

            <div class="form-check">
                <input class="form-check-input middle" type="checkbox" name="sub_type_flag" >
                <label class="form-check-label" for="sub_type_flag">Story</label>
            </div>

            <div class="form-check">
                <input class="form-check-input middle" type="checkbox" name="read_reverse" >
                <label class="form-check-label" for="read_reverse">Read Reverse</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="read_random" >
                <label class="form-check-label" for="read_random"">Read Random</label>
            </div>

			<div class="my-3">
				<button tabindex="-1" type="submit" name="update" class="btn btn-primary">@LANG('ui.Add')</button>
			</div>

			<div class="entry-description-div mb-3">
				<textarea rows="12" id="description" name="description" class="form-control" placeholder="@LANG('ui.Text')" onfocus="setFocus($(this), '#accent-chars')"></textarea>
			</div>

            @if (isset($languageOptions))
                <div><labe>{{trans_choice('ui.Language', 1)}}:</label></div>
                @component('components.control-dropdown-language', [
                    'options' => $languageOptions,
                    'selected_option' => $selectedOption,
                    'field_name' => 'language_flag',
                    'select_class' => 'mt-1 mb-3',
                ])@endcomponent
            @endif

			<div style="margin:20px 0;">
				<button tabindex="-1" type="submit" name="update" class="btn btn-primary">@LANG('ui.Add')</button>
			</div>

			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection

