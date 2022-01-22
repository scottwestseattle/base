@extends('layouts.app')
@section('title', __('proj.Delete Article'))
@section('menu-submenu')@component('gen.articles.menu-submenu', ['record' => $record]) @endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('proj.Delete Article')}}</h1>

	<form method="POST" action="/articles/delete/{{ $record->id }}">

		<div class="submit-button mb-3">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

		<h4>{{$record->title}}</h4>

        <div class="entry-div" style="margin-top:20px; width:100%; font-size:1.1em;">
            <div class="entry" style="width:100%;">
                <span name="description" class="">{!! $record->description !!}</span>
            </div>
        </div>

		<div class="submit-button mt-4">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

	{{ csrf_field() }}
	</form>
</div>
@endsection
