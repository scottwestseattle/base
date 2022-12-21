@extends('layouts.app')
@section('title', trans_choice('proj.Book', 2))
@section('menu-submenu')@component('gen.books.menu-submenu', ['index' => 'books', 'isIndex' => true])@endcomponent @endsection
@section('content')
@php
    $colors = [
        'Tomato',
        'MediumSeaGreen',
        'DodgerBlue',
        'Teal',
        'purple',
        'maroon',
        'orange',
        'Violet',
        'SlateBlue',
        'Olive',
        'DarkOrange',
    ];

    $colorIndex = 0;

@endphp
<div class="container page-normal">

	<h1>{{trans_choice('proj.Book', 2)}}<span class="title-count">({{count($books)}})</span></h1>

    <div class="row mb-3">
        @foreach($books as $record)
            @php $photo = file_exists(public_path() . '/img/books/' . $record->id . '.png'); @endphp
            <div class="text-center mb-2 ml-2"
            style="min-width:100px; max-width:45%; border-radius:10px; background-color: {{$photo ? 'default' : $colors[$colorIndex % 10]}};
                background-image:url('/img/books/pattern.png'); background-size:cover;">

                <a href="/books/chapters/{{$record->id}}">
                    @if ($photo)
                        <img style="height:230px;" src="/img/books/{{$record->id}}.png" />
                    @else
                        <div style="height:230px; width:151px;">
                            <div style="color: white; padding: 30% 20px; overflow-wrap:break-word; font-weight:bold; font-size:20px;">
                                {{$record->name}}
                            </div>
                        </div>
                        @php $colorIndex++ @endphp
                    @endif
                </a>

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
                <!-- End of Chapters -->

            </div>
        @endforeach
    </div>

	@if (isset($records) && count($records) > 0)
	<h1>@LANG('proj.Latest Chapters Viewed')</h1>
	<div>
		@foreach($records as $record)
		<div class="drop-box-ghost mb-4" style="padding:10px 10px 0px 15px;">
		    <div class="medium-thin-text" >{{$record->source}}</div>
			<div class="large-thin-text">
				<a href="/books/show/{{$record->permalink}}">{{$record->title}}</a>
			</div>

			<div class="small-thin-text middle" style="line-height:30px;">
				<div style="float:left;">
					@component('components.icon-read', ['href' => "/books/read/$record->id"])@endcomponent
					<div style="margin-right:15px; float:left;">{{$record->view_count}} {{trans_choice('ui.View', 2)}}</div>
					<div style="margin-right:15px; float:left;"><a href="/entries/stats/{{$record->id}}">{{str_word_count($record->description)}} {{trans_choice('ui.Word', 2)}}</a></div>
					@if (App\User::isAdmin())
						<div style="margin-right:15px; float:left;">
							@component('components.control-button-publish', ['record' => $record, 'prefix' => 'entries', 'showPublic' => true, 'ajax' => true, 'reload' => true])@endcomponent
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
	</div>
	@endif

</div>

@endsection
