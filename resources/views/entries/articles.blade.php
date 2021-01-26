@extends('layouts.app')

@section('content')

<div class="container page-normal">

	<h1>@LANG('ui.Articles') ({{count($records)}})</h1>

	<div>
	@if (isset($records))
		@foreach($records as $record)
		<div class="drop-box-ghost mb-4" style="padding:10px 10px 20px 15px;">
			<div style="font-size:1.3em; font-weight:normal;">
				<a href="/entries/{{$record->permalink}}">{{$record->title}}</a>
			</div>

			<div style="padding-bottom:10px; font-size:.8em; font-weight:10;">
				<div style="float:left;">
                    <div class="float-left mr-3"><img width="25" src="/img/flags/{{getSpeechLanguageShort($record->language_flag)}}.png" /></div>
					@component('components.icon-read', ['href' => "/entries/read/$record->id"])@endcomponent
					<div style="margin-right:15px; float:left;">{{$record->view_count}} @LANG('content.views')</div>
					<div style="margin-right:15px; float:left;"><a href="/entries/stats/{{$record->id}}">{{str_word_count($record->description)}} @LANG('content.words')</a></div>
					@if (App\User::isAdmin())
						<div style="margin-right:15px; float:left;">
							@component('components.control-button-publish', ['record' => $record, 'prefix' => 'entries', 'showPublic' => true])@endcomponent
						</div>
					@endif
				</div>
				<div style="float:left;">
					@if (App\User::isAdmin())
						<div style="margin-right:5px; float:left;"><a href='/entries/edit/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-edit"></span></a></div>
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
