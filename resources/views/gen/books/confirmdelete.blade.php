@extends('layouts.app')
@section('title', __('proj.Delete Chapter'))
@section('menu-submenu')@component('gen.books.menu-submenu', ['record' => $record, 'bookId' => $book->id]) @endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('proj.Delete Chapter')}}</h1>
	<form method="POST" action="{{route('books.delete', ['locale' => App()->getLocale(), 'entry' => $record->id])}}">

		<h4>{{$record->title}}</h4>

		<div class="submit-button mb-3">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

		<p>{{$record->description }}</p>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>

	{{ csrf_field() }}
	</form>
</div>
@endsection
