@extends('layouts.app')
@section('title', trans_choice('proj.Book', 2))
@section('menu-submenu')@component('gen.books.menu-submenu', ['index' => 'books', 'isIndex' => true, 'bookId' => $book->id])@endcomponent @endsection
@section('content')
@php
    $locale = app()->getLocale();
    $count = isset($book) ? count($book->books) : 0;
@endphp
<div class="container page-normal">

    <div class="mb-2">
        <a href="{{route('books', ['locale' => $locale])}}"><button type="button" class="btn btn-success mb-1"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-up"></span>{{trans_choice('proj.Back to Book', 2)}}</button></button></a>
    </div>

	<h1 class="mb-1">
	    <span class="mr-2">{{$book->name}}</span>
        @component('components.icon-read', ['href' => route('books.readBook', ['locale' => $locale, 'tag' => $book->id]), 'color' => '', 'nodiv' => true])@endcomponent
	</h1>

    <div class="medium-thin-text mb-3">{{trans_choice('proj.Chapter', 2)}}: {{$count}}<a class="ml-3" href="{{route('books.stats', ['locale' => $locale, 'tag' => $book->id])}}">Stats</a></div>

	<div>
	@if ($count > 0)
		@foreach($book->books as $record)
		<div class="drop-box-ghost mb-4" style="padding:10px 10px 0px 15px;">
			<div class="large-thin-text"><a href="{{route('books.show', ['locale' => $locale, 'permalink' => $record->permalink])}}">{{$record->title}}</a></div>

			<div class="small-thin-text middle" style="line-height:30px;">
				<div style="float:left;">
					@component('components.icon-read', ['href' => route('books.read', ['locale' => $locale, 'entry' => $record->id])])@endcomponent
					<div style="margin-right:15px; float:left;">{{$record->view_count}} {{trans_choice('ui.View', 2)}}</div>
					<div style="margin-right:15px; float:left;"><a href="{{route('entries.stats', ['locale' => $locale, 'entry' => $record->id])}}">{{str_word_count($record->description)}} {{trans_choice('ui.Word', 2)}}</a></div>
					@if (App\User::isAdmin())
						<div style="margin-right:15px; float:left;">
							@component('components.control-button-publish', ['record' => $record, 'prefix' => 'books', 'showPublic' => true, 'ajax' => true, 'reload' => true])@endcomponent
						</div>
					@endif
				</div>
				<div style="float:left;">
					@if (App\User::isAdmin())
					<div style="margin-right:5px; float:left;"><a href='{{route('books.edit', ['locale' => $locale, 'tag' => $record->id])}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-edit"></span></a></div>
					@component('components.control-delete-glyph', ['glyphicon' => 'glyphCustom glyphCustom-lt glyphicon-trash', 'href' => route('entries.delete', ['locale' => $locale, 'entry' => $record->id]), 'prompt' => 'Confirm Delete'])@endcomponent
					@endif
				</div>
				<div style="clear:both;"></div>
				@if (App\User::isSuperAdmin())
				<div class="mt-1">
					@if (App\User::isAdmin())
						<span>Site: {{$record->site_id}}</span>
					@endif
				</div>
				@endif
			</div>
		</div>
		@endforeach
	@endif
	</div>

</div>

@endsection
