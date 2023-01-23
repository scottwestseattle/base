@extends('layouts.app')
@section('title', __('proj.Choose Daily Practice Exercises'))
@section('menu-submenu')@component('gen.exercises.menu-submenu', ['prefix' => 'exercises'])@endcomponent @endsection
@section('content')
@php
    $userId = Auth::id();
    $activeIds = $parms['activeIds'];
@endphp
<div class="">
	<h1 class="mb-1">@LANG('proj.Choose Daily Practice Exercises')</h1>
	<form method="POST" action="/exercises/set">

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('ui.Save')</button>
		</div>

        <h2 class="mt-3">General Settings</h2>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="articles-otd">
            <label class="form-check-label" for="article-otd">
                @LANG('proj.Show Daily Exercises on Front Page')
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="random-article">
            <label class="form-check-label" for="random-article">
                @LANG('proj.Hide Completed Exercises')
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="daily-email">
            <label class="form-check-label" for="daily-email">
                @LANG('proj.Send Daily Email Reminder')
            </label>
        </div>

        <hr/>
        <h2 class="mt-3">Select Daily Practice Exercises</h2>

        @if (count($parms['templates']) > 0)
            @foreach($parms['templates'] as $record)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="template_{{$record->id}}" value="{{$record->id}}" {{isset($activeIds['template_' . $record->id]) ? 'checked' : ''}}>
                    <label class="form-check-label" for="template_{{$record->id}}">
                        @LANG('proj.' . $record->title)
                    </label>
                </div>
            @endforeach
        @endif

        @if ((isset($parms['favorites']) && count($parms['favorites']) > 0))
        <h3 class="mt-3">Favorites Lists</h3>
            @foreach($parms['favorites'] as $record)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="favorites_{{$record->id}}" value="{{$record->id}}" {{isset($activeIds['favorite_' . $record->id]) ? 'checked' : ''}}>
                    <label class="form-check-label" for="favorite_{{$record->id}}">
                        @LANG('proj.' . $record->name)
                    </label>
                </div>
            @endforeach
        @endif

        @if ((isset($parms['lessons']) && count($parms['lessons']) > 0))
        <h3 class="mt-3">Lesson Exercises</h3>
            @foreach($parms['lessons'] as $record)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="favorites_{{$record->id}}" value="{{$record->id}}" {{isset($activeIds[$record->id]) ? 'checked' : ''}}>
                    <label class="form-check-label" for="{{$record->id}}">
                        @LANG('proj.' . $record->name)
                    </label>
                </div>
            @endforeach
        @endif

        @if (false && count($parms['snippets']) > 0)
        <h3 class="mt-3">Practice Text</h3>
            @foreach($parms['snippets'] as $record)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="{{$record->id}}"">
                    <label class="form-check-label" for="{{$record->id}}">
                        @LANG('proj.' . $record->title)
                    </label>
                </div>
            @endforeach
        @endif

        @if (false && count($parms['lessons']) > 0)
        <h3 class="mt-3">Lessons</h3>
            @foreach($parms['lessons'] as $record)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="{{$record->id}}"">
                    <label class="form-check-label" for="{{$record->id}}">
                        @LANG('proj.' . $record->title)
                    </label>
                </div>
            @endforeach
        @endif

        @if (false)
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="lessons-1" id="1329">
            <label class="form-check-label" for="1329">
                @LANG('proj.Tricky Prepositions')
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="lessons-2" id="1303">
            <label class="form-check-label" for="1303">
                @LANG('proj.Género en General')
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="lessons-3" id="1330">
            <label class="form-check-label" for="1330">
                @LANG('proj.Phrasing')
            </label>
        </div>
        @endif

        @if (false)
        <h3 class="mt-3">Favorites List</h3>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="favorites-1" id="1000">
            <label class="form-check-label" for="1000">
                @LANG('proj.Hot List 1')
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="favorites-2" id="2000">
            <label class="form-check-label" for="2000">
                @LANG('proj.CORRECCIONES 2')
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="favorites-3" id="3000">
            <label class="form-check-label" for="3000">
                @LANG('proj.MEMORIZAR')
            </label>
        </div>
        @endif

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}
	</form>
</div>
@endsection
