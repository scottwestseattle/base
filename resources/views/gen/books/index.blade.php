@extends('layouts.app')
@section('title', trans_choice('proj.Book', 2))
@section('menu-submenu')@component('gen.books.menu-submenu', ['index' => 'books', 'isIndex' => true])@endcomponent @endsection
@section('content')

<div class="container page-normal">

	<h1>{{trans_choice('proj.Book', 2)}} ({{count($books)}})</h1>

	<div>
	@foreach($books as $record)
	<div class="drop-box-ghost mb-4" style="padding:10px;">
		<div style="font-size:1.3em; font-weight:normal;">
			<a href=""  onclick="event.preventDefault(); $('#parts{{$record->id}}').toggle();">{{$record->name}} ({{count($record->books)}} {{strtolower(trans_choice('proj.Chapter', 2))}})</a>
			<!-- The chapters are hidden until clicked on -->
			<div id="parts{{$record->id}}" class="mt-2 hidden">
			@foreach($record->books as $r)
			<div class="ml-2 mt-1" style="font-size:14px;">
				<div class="">
					<a href="/books/show/{{$r->permalink}}">{{$r->title}}</a>
				</div>
			</div>
			@endforeach
			</div>
		</div>
	</div>
	@endforeach
	</div>

	<h1>@LANG('proj.Latest Chapters Viewed')</h1>

	<div>
	@if (isset($records))
		@foreach($records as $record)
		<div class="drop-box-ghost mb-4" style="padding:10px 10px 20px 15px;">
			<div style="font-size:1.3em; font-weight:normal;">
				<a href="/books/show/{{$record->permalink}}">{{$record->title}}</a>
			</div>

			<div style="padding-bottom:10px; font-size:.8em; font-weight:10;">
				<div style="float:left;">
					@component('components.icon-read', ['href' => "/books/read/$record->id"])@endcomponent
					<div style="margin-right:15px; float:left;">{{$record->view_count}} {{trans_choice('ui.View', 2)}}</div>
					<div style="margin-right:15px; float:left;"><a href="/books/stats/{{$record->id}}">{{str_word_count($record->description)}} {{trans_choice('ui.Word', 2)}}</a></div>
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
