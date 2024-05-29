@extends('layouts.app')
@section('title', __('proj.Add Exercise'))
@section('menu-submenu')@component('gen.exercises.menu-submenu', ['prefix' => 'exercises'])@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('proj.Add Exercise')}}</h1>
	<form method="POST" action="{{route('exercises.create', ['locale' => $locale])}}">

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
                <option value="">(@LANG('ui.Select'))</option>
                <option value="{{HISTORY_TYPE_ARTICLE}}">Article</option>
                <option value="{{HISTORY_TYPE_BOOK}}">Book</option>
                <option value="{{HISTORY_TYPE_DICTIONARY}}">Dictionary Words</option>
                <option value="{{HISTORY_TYPE_DICTIONARY_VERBS}}">Dictionary Verbs</option>
                <option value="{{HISTORY_TYPE_SNIPPETS}}">Practice Text</option>
                <option value="{{HISTORY_TYPE_FAVORITES}}">Favorites List</option>
                <option value="{{HISTORY_TYPE_LESSON}}">Lesson Exercise</option>
            </select>
        </div>

       <div class="form-group">
            <label for="subtype_flag" class="control-label">@LANG('proj.Content Order'):</label>
            <select name="subtype_flag" class="form-control">
                <option value="">(@LANG('ui.Select'))</option>
                <option value="{{HISTORY_SUBTYPE_EXERCISE_OTD}}">{{App\Gen\Exercise::getSubTypeFlagName(HISTORY_SUBTYPE_EXERCISE_OTD)}}</option>
                <option value="{{HISTORY_SUBTYPE_EXERCISE_RANDOM}}">Random</option>
                <option value="{{HISTORY_SUBTYPE_EXERCISE_NEWEST}}">Newest</option>
                <option value="{{HISTORY_SUBTYPE_EXERCISE_LEAST_USED}}">Least Used</option>
                <option value="{{HISTORY_SUBTYPE_EXERCISE_MOST_COMMON}}">Most Common</option>
            </select>
        </div>

        <div class="form-group">
            <label for="action_flag" class="control-label">@LANG('ui.Action'):</label>
            <select name="action_flag" class="form-control">
                <option value="">(@LANG('ui.Select'))</option>
                <option value="{{LESSON_TYPE_READER}}">Read</option>
                <option value="{{LESSON_TYPE_QUIZ_FLASHCARDS}}">Flashcards</option>
                <option value="{{LESSON_TYPE_OTHER}}">Inherit from Lesson</option>
            </select>
        </div>

		<div class="form-group">
			<label for="url" class="control-label">@LANG('ui.URL'):</label>
			<input type="text" name="url" class="form-control" />
        </div>

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('base.Add')</button>
		</div>

		{{ csrf_field() }}
	</form>
</div>
@endsection
