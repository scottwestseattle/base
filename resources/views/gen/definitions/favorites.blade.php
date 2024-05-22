@php
    $order = 'desc';
    $locale = app()->getLocale();
@endphp
@extends('layouts.app')
@section('title', trans_choice('ui.List', 2))
@section('menu-submenu')@component('tags.menu-submenu')@endcomponent @endsection
@section('content')
<!--------------------------------------------------------------------->
<!-- Favorites Lists                                                 -->
<!--------------------------------------------------------------------->

@if (isset($favorites))
<h1 class="mb-0">{{trans_choice('ui.Favorite', 2)}}
    <a class="btn btn-info btn-xs" role="button" href="{{route('tags.addUserFavoriteList', ['locale' => $locale])}}">
        @LANG('ui.Add New List')<span class="glyphicon glyphicon-plus-sign ml-1"></span>
    </a>
</h1>
<div class="mb-2 small-thin-text">@LANG('proj.Vocabulary favorited from dictionary')</div>

@if (count($favorites) > 0)
<div class="card-deck">
<!--------------------------------------------------------------------->
<!-- All Favorites                                                   -->
<!--------------------------------------------------------------------->
@if (isset($favoritesCnt) && $favoritesCnt > 0)
<div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
    <div class="mb-3 mr-0">
        <div class="card-body drop-box-ghost">
            <h5 class="card-title">
                <a href="{{route('definitions.favoritesReview', ['locale' => $locale])}}?order={{$order}}">All</a>@component('components.badge', ['text' => $favoritesCnt])@endcomponent
            </h5>
            <p class="card-text">
                <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.favoritesReview', ['locale' => $locale])}}?action=flashcards&count={{$favoritesCnt}}&order={{$order}}">
                    @LANG('proj.Flashcards') ({{$favoritesCnt}})<span class="glyphicon glyphicon-flash ml-1"></span>
                </a>
                <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.favoritesReview', ['locale' => $locale])}}?action=flashcards&count=20&order={{$order}}">
                    @LANG('proj.Flashcards') (20)<span class="glyphicon glyphicon-flash ml-1"></span>
                </a>
                <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.favoritesReview', ['locale' => $locale])}}?action=read&count={{$favoritesCnt}}&order={{$order}}">
                    @LANG('proj.Reader') ({{$favoritesCnt}})<span class="glyphicon glyphicon-volume-up ml-1"></span>
                </a>
            </p>
        </div>
    </div>
</div>
@endif
<!--------------------------------------------------------------------->
<!-- Favorites Lists                                                 -->
<!--------------------------------------------------------------------->
@foreach($favorites as $record)
<div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
    <div class="mb-3 mr-0">
        <div class="card-body drop-box-ghost">
            <h5 class="card-title">
                <a href="{{route('definitions.listTag', ['locale' => $locale, 'tag' => $record->id])}}?order={{$order}}">{{$record->name}}</a>@component('components.badge', ['text' => count($record->definitions)])@endcomponent
            </h5>
            <p class="card-text">
                <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.review', ['locale' => $locale, 'tag' => $record->id])}}">
                    @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                </a>
                <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.favoritesReview', ['locale' => $locale])}}?action=flashcards&count=20&tagId={{$record->id}}&order={{$order}}">
                    @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                </a>
                <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.favoritesReview', ['locale' => $locale])}}?action=read&count={{DEFAULT_BIG_NUMBER}}&tagId={{$record->id}}&order={{$order}}">
                    @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
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
        <div class="medium-thin-text mb-5 ml-3"><a href="{{route('login', ['locale' => $locale])}}">@LANG('ui.Login')</a> @LANG('ui.or') <a href="/register">@LANG('proj.Register to create lists')</a></div>
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
<!--------------------------------------------------------------------->
<!-- Dictionary Lists                                                -->
<!--------------------------------------------------------------------->

@if (isset($newest))
<h1 class="mb-0">@LANG('proj.Dictionary')</h1>
<div class="mb-2 small-thin-text ml-1">@LANG('proj.Lists from the dictionary')</div>
<div class="card-deck mb-4">

    <div class="col-sm-12 col-lg-6 col-xl-4">
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="/practice/index?count=20&sort='desc'">@LANG('proj.Latest Practice Text')</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="/snippets/review?action=flashcards&count=20&order=desc">
                        @LANG('proj.Flashcards') (20)<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/snippets/read?count=20&order=desc&return=favorites">
                        @LANG('proj.Reader') (20)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/snippets/read?count=100&order=desc&return=favorites">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4">
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="/definitions/read-examples?count=20">@LANG('proj.Dictionary Examples')</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/read-examples?action=read">
                        @LANG('proj.Reader') (20)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/read-examples?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/read-examples?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4">
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="/definitions/review-top-20-verbs">@LANG('proj.:count Most Common Verbs', ['count' => 20])</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-top-20-verbs?action=quiz">
                        @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-top-20-verbs?action=flashcards">
                        @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-top-20-verbs?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-top-20-verbs?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="/definitions/review-newest">@LANG('proj.:count Newest Words', ['count' => 20])</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest?action=quiz">
                        @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest?action=flashcards">
                        @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="/definitions/review-newest-verbs">@LANG('proj.:count Newest Verbs', ['count' => 20])</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest-verbs?action=quiz">
                        @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest-verbs?action=flashcards">
                        @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest-verbs?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-newest-verbs?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="/definitions/review-random-words">@LANG('proj.:count Random Words', ['count' => 20])</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-words?action=quiz">
                        @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-words?action=flashcards">
                        @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-words?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-words?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="/definitions/review-random-verbs">@LANG('proj.:count Random Verbs', ['count' => 20])</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-verbs?action=quiz">
                        @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-verbs?action=flashcards">
                        @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-verbs?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="/definitions/review-random-verbs?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

</div>
@endif

@endsection
