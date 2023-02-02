@extends('layouts.app')
@section('title', __('proj.Choose Daily Practice Exercises'))
@section('content')
@php
    $enabled = $parms['enabled'];
    if ($enabled)
    {
        $userId = Auth::id();
        $activeIds = $parms['activeIds'];
    }
@endphp
<div class="">
@if ($enabled)
	<h1 class="mb-1">@LANG('proj.Choose Daily Practice Exercises')</h1>
	<form method="POST" action="/exercises/set">

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('ui.Save')</button>
		</div>

        @if (false)
        <h2 class="mt-3">@LANG('ui.General Settings')</h2>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="show-front-page">
            <label class="form-check-label" for="show-front-page">
                @LANG('proj.Show Daily Exercises on Front Page')
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="hide-completed">
            <label class="form-check-label" for="hide-completed">
                @LANG('proj.Hide Completed Exercises')
            </label>
        </div>
        <!-- div class="form-check">
            <input class="form-check-input" type="checkbox" name="daily-email">
            <label class="form-check-label" for="daily-email">
                @LANG('proj.Send Daily Email Reminder')
            </label>
        </div -->

        <hr/>
        @endif

        <h2 class="mt-3">@LANG('proj.Select Exercises')</h2>

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
        <h2 class="mt-3">{{trans_choice('proj.Favorites List', 2)}}</h2>
            @foreach($parms['favorites'] as $record)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="favorites_{{$record->id}}" value="{{$record->id}}" {{isset($activeIds['favorite_' . $record->id]) ? 'checked' : ''}}>
                    <label class="form-check-label" for="favorite_{{$record->id}}">
                        @LANG($record->name)
                    </label>
                </div>
            @endforeach
        @endif

        @if ((isset($parms['lessons']) && count($parms['lessons']) > 0))
        <h3 class="mt-3">@LANG('proj.Lesson Exercises')</h3>
            @foreach($parms['lessons'] as $record)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="favorites_{{$record->id}}" value="{{$record->id}}" {{isset($activeIds[$record->id]) ? 'checked' : ''}}>
                    <label class="form-check-label" for="{{$record->id}}">
                        @LANG('proj.' . $record->name)
                    </label>
                </div>
            @endforeach
        @endif

		<div class="form-group">
			<button type="submit" name="update" class="mt-3 btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}
	</form>
@else
    <h1>@LANG('proj.No Exercises Available')</h1>
@endif
</div>
@endsection
