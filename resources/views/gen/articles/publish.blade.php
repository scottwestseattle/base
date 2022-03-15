@extends('layouts.app')
@section('title', __('proj.Publish Article'))
@section('menu-submenu')@component('gen.articles.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')

<h1>{{__('proj.Publish Article')}}</h1>

<form method="POST" action="/articles/publishupdate/{{$record->id}}">

    <h3 name="title" class="">{{$record->title}}</h3>

    <div class="form-group">
        <label for="wip_flag" class="control-label">@LANG('base.Work Status'):</label>
        <select name="wip_flag" class="form-control">
            @foreach ($wip_flags as $key => $value)
                <option value="{{$key}}" {{ $key == $record->wip_flag ? 'selected' : ''}}>{{__($value)}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="release_flag" class="control-label">@LANG('base.Release Status'):</label>
        <select name="release_flag" class="form-control">
            @foreach ($release_flags as $key => $value)
                <option value="{{$key}}" {{ $key == $record->release_flag ? 'selected' : ''}}>{{__($value)}}</option>
            @endforeach
        </select>
    </div>

	<div class="submit-button">
		<button type="submit" class="btn btn-primary">@LANG('ui.Update')</button>
	</div>
    {{ csrf_field() }}
</form>

@endsection
