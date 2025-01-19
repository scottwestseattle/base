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
<h1 class="mb-0">{{trans_choice('ui.Favorite', 2)}}<span class="title-count">({{count($favorites)}})</span>
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
                <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.favoritesReview', ['locale' => $locale])}}?action=read&count={{50/*DEFAULT_BIG_NUMBER*/}}&tagId={{$record->id}}&order={{$order}}">
                    @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                </a>
                <a href='{{route('tags.edit', ['locale' => $locale, 'tag' => $record->id])}}'><span class="glyphCustom-sm glyphicon glyphicon-edit"></span></a></td>
                <a href='{{route('tags.confirmUserFavoriteListDelete', ['locale' => $locale, 'tag' => $record->id])}}'><span class="glyphCustom-sm glyphicon glyphicon-delete"></span></a>
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
        <div class="medium-thin-text mb-5 ml-3"><a href="{{route('login', ['locale' => $locale])}}">@LANG('ui.Login')</a> @LANG('ui.or') <a href="{{route('register', ['locale' => $locale])}}">@LANG('proj.Register to create lists')</a></div>
    @endif
@endif
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
                <h5 class="card-title"><a href="{{route('practice.index', ['locale' => $locale])}}?count=20&sort='desc'">@LANG('proj.Latest Practice Text')</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('snippets.review', ['locale' => $locale])}}?action=flashcards&count=20&order=desc">
                        @LANG('proj.Flashcards') (20)<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('snippets.read', ['locale' => $locale])}}?count=20&order=desc&return=favorites">
                        @LANG('proj.Reader') (20)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('snippets.read', ['locale' => $locale])}}?count=100&order=desc&return=favorites">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4">
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="{{route('definitions.readExamples', ['locale' => $locale])}}?count=20">@LANG('proj.Dictionary Examples')</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.readExamples', ['locale' => $locale])}}?action=read">
                        @LANG('proj.Reader') (20)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.readExamples', ['locale' => $locale])}}?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.readExamples', ['locale' => $locale])}}?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4">
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="{{route('definitions.reviewTop20Verbs', ['locale' => $locale])}}">@LANG('proj.:count Most Common Verbs', ['count' => 20])</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewTop20Verbs', ['locale' => $locale])}}?action=quiz">
                        @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewTop20Verbs', ['locale' => $locale])}}?action=flashcards">
                        @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewTop20Verbs', ['locale' => $locale])}}?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewTop20Verbs', ['locale' => $locale])}}?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="{{route('definitions.reviewNewest', ['locale' => $locale])}}">@LANG('proj.:count Newest Words', ['count' => 20])</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewNewest', ['locale' => $locale])}}?action=quiz">
                        @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewNewest', ['locale' => $locale])}}?action=flashcards">
                        @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewNewest', ['locale' => $locale])}}?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewNewest', ['locale' => $locale])}}?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="{{route('definitions.reviewNewestVerbs', ['locale' => $locale])}}">@LANG('proj.:count Newest Verbs', ['count' => 20])</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewNewestVerbs', ['locale' => $locale])}}?action=quiz">
                        @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewNewestVerbs', ['locale' => $locale])}}?action=flashcards">
                        @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewNewestVerbs', ['locale' => $locale])}}?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewNewestVerbs', ['locale' => $locale])}}?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="{{route('definitions.reviewRandomWords', ['locale' => $locale])}}">@LANG('proj.:count Random Words', ['count' => 20])</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewRandomWords', ['locale' => $locale])}}?action=quiz">
                        @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewRandomWords', ['locale' => $locale])}}?action=flashcards">
                        @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewRandomWords', ['locale' => $locale])}}?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewRandomWords', ['locale' => $locale])}}?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-6 col-xl-4"><!-- xl = 3 cols, lg = 2 cols, sm = 1 col -->
        <div class="mb-3 mr-0">
            <div class="card-body drop-box-ghost">
                <h5 class="card-title"><a href="{{route('definitions.reviewRandomVerbs', ['locale' => $locale])}}">@LANG('proj.:count Random Verbs', ['count' => 20])</a></h5>
                <p class="card-text">
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewRandomVerbs', ['locale' => $locale])}}?action=quiz">
                        @LANG('ui.Review')<span class="glyphicon glyphicon-eye-open ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewRandomVerbs', ['locale' => $locale])}}?action=flashcards">
                        @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewRandomVerbs', ['locale' => $locale])}}?action=read">
                        @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                    <a class="btn btn-primary btn-xs" role="button" href="{{route('definitions.reviewRandomVerbs', ['locale' => $locale])}}?action=read&count=100">
                        @LANG('proj.Reader') (100)<span class="glyphicon glyphicon-volume-up ml-1"></span>
                    </a>
                </p>
            </div>
        </div>
    </div>

</div>
@endif

@endsection
