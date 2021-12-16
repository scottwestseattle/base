@extends('layouts.app')
@section('title', trans_choice('proj.Book', 2))
@section('menu-submenu')@component('gen.books.menu-submenu', ['index' => 'books', 'isIndex' => true])@endcomponent @endsection
@section('content')

<div class="container page-normal">

    <div class="mb-2">
        <a href="/books/"><button type="button" class="btn btn-success mb-1"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-up"></span>{{trans_choice('proj.Back to Book', 2)}}</button></button></a>
    </div>

	<h1>
	    {{$book->name}}
        @component('components.icon-read', ['href' => "/books/read-book/$book->id", 'color' => '', 'nodiv' => true])@endcomponent
	</h1>

	<div>
	@if (isset($book))
		@foreach($book->books as $record)
		<div class="drop-box-ghost mb-4" style="padding:10px 10px 20px 15px;">
			<div style="font-size:1.3em; font-weight:normal;">
				<a href="/books/show/{{$record->permalink}}">{{$record->title}}</a>
			</div>

			<div style="padding-bottom:10px; font-size:.8em; font-weight:10;">
				<div style="float:left;">
					@component('components.icon-read', ['href' => "/books/read/$record->id"])@endcomponent
					<div style="margin-right:15px; float:left;">{{$record->view_count}} {{trans_choice('ui.View', 2)}}</div>
					<div style="margin-right:15px; float:left;"><a href="/entries/stats/{{$record->id}}">{{str_word_count($record->description)}} {{trans_choice('ui.Word', 2)}}</a></div>
					@if (App\User::isAdmin())
						<div style="margin-right:15px; float:left;">
							@component('components.control-button-publish', ['record' => $record, 'prefix' => 'entries', 'showPublic' => true])@endcomponent
						</div>
					@endif
				</div>
				<div style="float:left;">
					@if (App\User::isAdmin())
					<div style="margin-right:5px; float:left;"><a href='/books/edit/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-edit"></span></a></div>
					@component('components.control-delete-glyph', ['glyphicon' => 'glyphCustom glyphCustom-lt glyphicon-trash', 'href' => '/entries/delete/' . $record->id . '', 'prompt' => 'Confirm Delete'])@endcomponent
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
