@extends('layouts.app')
@section('title', __('proj.Add Exercise'))
@section('menu-submenu')@component('gen.exercises.menu-submenu', ['prefix' => 'exercises'])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.Add Exercise')}}</h1>
	<form method="POST" action="/exercises/create">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('base.Title'):</label>
			<input type="text" name="title" class="form-control @error('model') is-invalid @enderror" required autofocus />
			@error('title')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
			@enderror
        </div>

        <div class="form-group">
            <label for="type_flag" class="control-label">@LANG('proj.Content Type'):</label>
            <select name="type_flag" class="form-control">
                <option value="">(Select Item)</option>
                <option value="{{HISTORY_TYPE_ARTICLE}}">Article</option>
                <option value="{{HISTORY_TYPE_BOOK}}">Book</option>
                <option value="{{HISTORY_TYPE_DICTIONARY}}">Dictionary Words</option>
                <option value="{{HISTORY_TYPE_DICTIONARY_VERBS}}">Dictionary Verbs</option>
                <option value="{{HISTORY_TYPE_SNIPPETS}}">Practice Text</option>
                <option value="{{HISTORY_TYPE_FAVORITES}}">Favorites List</option>
                <option value="{{HISTORY_TYPE_EXERCISE}}">Lesson Exercise</option>
            </select>
        </div>

       <div class="form-group">
            <label for="subtype_flag" class="control-label">@LANG('proj.Content Order'):</label>
            <select name="subtype_flag" class="form-control">
                <option value="">(Select Item)</option>
                <option value="{{HISTORY_SUBTYPE_EXERCISE_OTD}}">OTD</option>
                <option value="{{HISTORY_SUBTYPE_EXERCISE_RANDOM}}">Random</option>
                <option value="{{HISTORY_SUBTYPE_EXERCISE_NEWEST}}">Newest</option>
                <option value="{{HISTORY_SUBTYPE_EXERCISE_LEAST_USED}}">Least Viewed</option>
                <option value="{{HISTORY_SUBTYPE_EXERCISE_MOST_COMMON}}">Most Common</option>
            </select>
        </div>

        <div class="form-group">
            <label for="action_flag" class="control-label">@LANG('proj.Action'):</label>
            <select name="action_flag" class="form-control">
                <option value="">(Select Item)</option>
                <option value="{{LESSON_TYPE_READER}}">Read</option>
                <option value="{{LESSON_TYPE_QUIZ_FLASHCARDS}}">Flashcards</option>
                <option value="{{LESSON_TYPE_OTHER}}">Inherit from Lesson</option>
            </select>
        </div>

        @if (false)
		<div class="form-group">
			<label for="title" class="control-label">@LANG('ui.Program Name'):</label>
			<input type="text" name="title" class="form-control @error('model') is-invalid @enderror" required />
        </div>

		<div class="form-group">
			<label for="title" class="control-label">@LANG('ui.Program ID'):</label>
			<input type="numeric" name="title" class="form-control @error('model') is-invalid @enderror" required />
        </div>
        @endif

		<div class="form-group">
			<label for="url" class="control-label">@LANG('ui.URL'):</label>
			<input type="text" name="url" class="form-control" />
        </div>

        @if (false)
		<div class="form-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="template">
                <label class="form-check-label" for="template">
                    {{trans_choice('ui.Template', 1)}}
                </label>
            </div>
        </div>
        @endif

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('base.Add')</button>
		</div>

		{{ csrf_field() }}
	</form>
</div>
@endsection
