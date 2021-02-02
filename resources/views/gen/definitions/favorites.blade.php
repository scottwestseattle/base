@extends('layouts.app')
@section('title', trans_choice('ui.List', 2))
@section('menu-submenu')@component('tags.menu-submenu')@endcomponent @endsection
@section('content')

	<!--------------------------------------------------------------------->
	<!-- Dictionary Lists                                                -->
	<!--------------------------------------------------------------------->

	@if (isset($newest))
	<h1 class="mb-0">@LANG('proj.Dictionary')</h1>
	<div class="mb-2 small-thin-text ml-1">@LANG('proj.Lists from the dictionary')</div>
    <div class="card-deck">

		<div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
			<div class="mb-3 mr-0">
				<div class="card-body drop-box-ghost">
					<h5 class="card-title">@LANG('proj.20 Newest Words')</h5>
					<p class="card-text">
						<a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest">
							@LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
						</a>
						<a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest/1">
							@LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
						</a>
					</p>
				</div>
			</div>
		</div>

		<div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
			<div class="mb-3 mr-0">
				<div class="card-body drop-box-ghost">
					<h5 class="card-title">@LANG('proj.20 Newest Verbs')</h5>
					<p class="card-text">
						<a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest-verbs">
							@LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
						</a>
						<a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest-verbs/1">
							@LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
						</a>
					</p>
				</div>
			</div>
		</div>

		<div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
			<div class="mb-3 mr-0">
				<div class="card-body drop-box-ghost">
					<h5 class="card-title">@LANG('proj.20 Random Words')</h5>
					<p class="card-text">
						<a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-words">
							@LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
						</a>
						<a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-words/1">
							@LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
						</a>
					</p>
				</div>
			</div>
		</div>

		<div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
			<div class="mb-3 mr-0">
				<div class="card-body drop-box-ghost">
					<h5 class="card-title">@LANG('proj.20 Random Verbs')</h5>
					<p class="card-text">
						<a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-verbs">
							@LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
						</a>
						<a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-verbs/1">
							@LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
						</a>
					</p>
				</div>
			</div>
		</div>

    </div>
	@endif

	<!--------------------------------------------------------------------->
	<!-- Favorites Lists                                                 -->
	<!--------------------------------------------------------------------->

	@if (isset($favorites))
	<h1 class="mb-0">@LANG('proj.Favorites')
		<a class="btn btn-info btn-xs" role="button" href="/tags/add-user-favorite-list">
			@LANG('ui.Add') @LANG('ui.List')<span class="glyphicon glyphicon-plus-sign ml-1"></span>
		</a>
	</h1>
	<div class="mb-2 small-thin-text">@LANG('proj.Vocabulary favorited from dictionary')</div>

	@if (count($favorites) > 0)
    <div class="card-deck">
    @foreach($favorites as $record)
	<div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
		<div class="mb-3 mr-0">
			<div class="card-body drop-box-ghost">
				<h5 class="card-title">
					<a href="/definitions/list/{{$record->id}}">{{$record->name}}</a>@component('components.badge', ['text' => $record->wc])@endcomponent
				</h5>
				<p class="card-text">
					<a class="btn btn-primary btn-xs" role="button" href="/definitions/review/{{$record->id}}">
						@LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
					</a>
					<a href='/tags/edit/{{$record->id}}'><span class="glyphCustom-sm glyphicon glyphicon-edit"></span></a></td>
					<a href='/tags/confirm-user-favorite-list-delete/{{$record->id}}'><span class="glyphCustom-sm glyphicon glyphicon-delete"></span></a>
				</p>
			</div>
		</div>
	</div>
    @endforeach
    </div>
	@else
	    @if (Auth::check())
    		<div class="medium-thin-text mb-5 ml-3">@LANG('proj.No lists')</div>
		@else
    		<div class="medium-thin-text mb-5 ml-3"><a href="/login">@LANG('ui.Login')</a> @LANG('ui.or') <a href="/register">@LANG('ui.Register')</a> @LANG('proj.to create lists')</div>
		@endif
	@endif
	@endif

	<!--------------------------------------------------------------------->
	<!-- Lists from Articles/Books                                       -->
	<!--------------------------------------------------------------------->

	@if (isset($entries) && count($entries) > 0)
	<h1 class="mb-0">@LANG('proj.Articles')</h1>
	<div class="mb-2 small-thin-text">@LANG('proj.Vocabulary saved from articles')</div>

    <div class="card-deck">
    @foreach($entries as $record)
	<div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
		<div class="mb-3 mr-0">
			<div class="card-body drop-box-ghost">
				<h5 class="card-title">
					<a href="/entries/vocabulary/{{$record->id}}">{{$record->title}}</a>@component('components.badge', ['text' => $record->wc])@endcomponent
				</h5>
				<p class="card-text">
					<a class="btn btn-primary btn-xs" role="button" href="/entries/review-vocabulary/{{$record->id}}">
						@LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
					</a>
				</p>
			</div>
		</div>
	</div>
    @endforeach
    </div>
	@endif

@endsection
