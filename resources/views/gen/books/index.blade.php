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

	<h1>{{trans_choice('proj.Book', 2)}} ({{count($books)}})</h1>

@if (true)
    <div class="row">
	@foreach($books as $record)
	    @php $photo = file_exists(public_path() . '/img/books/' . $record->id . '.png'); @endphp
        <div class="col-sm-3 text-center mb-4" style="min-width:200px;">

            <div class="card" style="border-radius:{{$photo ? '0' : '10'}}px;">
                <div class="card-header" style="padding: {{$photo ? '0' : 'default'}}; border-radius:10px 10px 0px 0px; background-color: {{$photo ? 'default' : $colors[$colorIndex % 10]}};">
                    <a href="" onclick="event.preventDefault(); $('#parts{{$record->id}}').toggle();">
                        @if ($photo)
                            <img style="height:205px; max-width:95%;" src="/img/books/{{$record->id}}.png" />
                        @else
                            <div style="height:180px; width:100%;">
                                <div style="color: white; padding: 10% 20px; overflow-wrap:break-word; font-weight:bold; font-size:20px;">{{$record->name}}</div>
                            </div>
                        @php $colorIndex++ @endphp
                        @endif
                    </a>

                </div>

                <div class="card-body">
                    <p class="">Read All&nbsp;@component('components.icon-read', ['href' => "/books/read-book/$record->id", 'color' => '', 'nodiv' => true])@endcomponent<p>
                </div>

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

@else

	<div>
	@foreach($books as $record)
	<div class="drop-box-ghost mb-4" style="padding:10px;">
		<div style="font-size:1.3em; font-weight:normal;">

			<a href=""  onclick="event.preventDefault(); $('#parts{{$record->id}}').toggle();">
			    {{$record->name}} ({{count($record->books)}} {{strtolower(trans_choice('proj.Chapter', 2))}})
			</a>&nbsp;@component('components.icon-read', ['href' => "/books/read-book/$record->id", 'color' => '', 'nodiv' => true])@endcomponent

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

@endif

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
