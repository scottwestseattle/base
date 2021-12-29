@extends('layouts.app')
@section('title', __('proj.Add Chapter'))
@section('menu-submenu')@component('gen.books.menu-submenu', [])@endcomponent @endsection
@section('content')

<div class="container page-normal">


    @if (isset($book))
    	<h1>{{__('proj.Add Chapter')}}</h1>
    	<h2>{{$book->name}}</h2>
    @else
        <h1>@LANG('proj.Add Book')</h1>
    @endif
	<form method="POST" action="/books/create">
		<div class="form-control-big">

			@if (isset($parent_id))
				<input type="hidden" name="parent_id" value="{{$parent_id}}">
			@endif
            @if (isset($book))
                <input type="hidden" name="book_id" id="book_id" value="{{$book->id}}" />
                <input type="hidden" name="source" id="source" value="{{$book->name}}" />
            @else
            @if (!isset($book))
                <div class="entry-title-div mb-3">
                    <input type="text" id="source" name="source" placeholder="Name of Book" onfocus="setFocus($(this), '#accent-chars')" class="form-control" autofocus />
                </div>
			@endif

            @endif

            @if (true)
			<div class="entry-title-div mb-3 mt-2">
				<input type="text" id="title" name="title" placeholder="@LANG('proj.Chapter Title')" onfocus="setFocus($(this), '#accent-chars')" class="form-control" />
			</div>
            @else
			<div class="entry-title-div mb-3 mt-2">
				<input onblur="javascript:urlEncode('title', 'permalink')" type="text" id="title" name="title" placeholder="@LANG('proj.Chapter Title')" onfocus="setFocus($(this), '#accent-chars')" class="form-control" autofocus />
			</div>

			<div class="entry-title-div mb-3">
				<input tabindex="-1" type="text" id="permalink" name="permalink" class="form-control"  placeholder="Permalink" />
			</div>
			@endif

            <div class="entry-title-div mb-3">
                <input type="text" id="display_order" name="display_order" placeholder="@LANG('proj.Chapter Number')" class="form-control" value="{{isset($chapter) ? $chapter : ''}}"
                />
            </div>

			<div class="entry-title-div mb-3">
				<input type="text" id="source_credit" name="source_credit" placeholder="@LANG('ui.Author')" onfocus="setFocus($(this), '#accent-chars')" class="form-control" />
			</div>

			<div class="entry-title-div mb-3">
				<input type="text" id="source_link" name="source_link" placeholder="@LANG('proj.Source Link')" class="form-control" />
			</div>

			<div class="entry-description-div mb-3">
				<textarea rows="3" id="description_short" name="description_short" class="form-control" placeholder="@LANG('ui.Summary')" onfocus="setFocus($(this), '#accent-chars')"></textarea>
			</div>

			<div class="mt-3 mb-3">
				<button tabindex="-1" type="submit" name="update" class="btn btn-primary">@LANG('ui.Add')</button>
			</div>

			<div class="entry-description-div mb-3">
				<textarea rows="12" id="description" name="description" class="form-control" placeholder="@LANG('ui.Description')" onfocus="setFocus($(this), '#accent-chars')"></textarea>
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

