@extends('layouts.app')
@section('title', __('ui.Site Map') )
@section('content')
@php
    $classH3 = 'mt-3 mb-1';
@endphp
<div class="container page-normal">

    <h1>@LANG('ui.Site Map')</h1>

    <h3 class="{{$classH3}}">Global</h3>
    <div><a target="_blank" href="/">Home</a></div>
    <div><a target="_blank" href="/about">About</a></div>
    <div><a target="_blank" href="/search">Search</a></div>
    <div><a target="_blank" href="/sites/sitemap">User Site Map</a></div>
    <hr/>

    <h3 class="{{$classH3}}">Articles</h3>
    <div><a target="_blank" href="/articles">Articles</a></div>
    <div><a target="_blank" href="/articles/index/date">Articles - List By Date</a></div>
    <div><a target="_blank" href="/articles/view/la-vida-a-salvo-en-la-dignidad-de-un-teniente">Show Article</a></div>
    <div><a target="_blank" href="/articles/read/22">Read Article</a></div>
    <div><a target="_blank" href="/articles/add">Add Article</a></div>
    <hr/>

    <h3 class="{{$classH3}}">Books</h3>
    <div><a target="_blank" href="/books">Books</a></div>
    <div><a target="_blank" href="/books/chapters/48">Book - Chapters</a></div>
    <div><a target="_blank" href="/books/show/capitulo-1-9706ec">Book - Show Chapter 1</a></div>
    <div><a target="_blank" href="/books/read/585">Book - Read Chapter 1</a></div>
    <hr/>

    <h3 class="{{$classH3}}">Courses</h3>
    <div><a target="_blank" href="/courses">Course List</a></div>
    <div><a target="_blank" href="/lessons/view/1320">View Course</a></div>

    <hr/>
    <div><a target="_blank" href="/lessons/view/1303">Género en General</a></div>
    <div><a target="_blank" href="/lessons/review/1303/2">Género en General - Quiz</a></div>
    <div><a target="_blank" href="/lessons/review/1303/2?count=20">Género en General - Quiz (20)</a></div>
    <hr/>
    <div><a target="_blank" href="/lessons/view/1303">Phrasing</a></div>
    <div><a target="_blank" href="/lessons/review/1330/1">Phrasing - Flashcards</a></div>
    <div><a target="_blank" href="/lessons/review/1330/1?count=20">Phrasing - Flashcards (20)</a></div>
    <hr/>

    <h3 class="{{$classH3}}">Dictionary</h3>
    <div><a target="_blank" href="/dictionary">Dictionary</a></div>
    <div><a target="_blank" href="/dictionary/search/1">Dictionary - (A/Z)</a></div>
    <div><a target="_blank" href="/dictionary/search/2">Dictionary - (Z/A)</a></div>
    <div><a target="_blank" href="/dictionary/search/10">Dictionary - All</a></div>
    <div><a target="_blank" href="/dictionary/search/14">Dictionary - Most Common</a></div>
    <div><a target="_blank" href="/dictionary/search/9">Dictionary - Verbs</a></div>
    <div><a target="_blank" href="/dictionary/search/15">Dictionary - Most Common Verbs</a></div>
    <div><a target="_blank" href="/dictionary/search/3">Dictionary - Newest Words</a></div>
    <div><a target="_blank" href="/dictionary/search/4">Dictionary - Most Recently Viewed</a></div>
    <hr/>

    <h3 class="{{$classH3}}">Favorites</h3>
    <div><a target="_blank" href="/favorites">Favorites</a></div>
    <div><a target="_blank" href="/tags/add-user-favorite-list">Add Favorites List</a></div>
    <hr/>
    <div><a target="_blank" href="/snippets/read?count=20&order=desc">Latest Practice Text - Read (20)</a></div>
    <div><a target="_blank" href="/snippets/read?count=100&order=desc">Latest Practice Text - Read (100)</a></div>
    <div><a target="_blank" href="/snippets/review/flashcards/20">Latest Practice Text - Flashcards (20)</a></div>
    <hr/>
    <div><a target="_blank" href="/definitions/read-examples?count=20">Dictionary Examples - List (20)</a></div>
    <div><a target="_blank" href="/definitions/read-examples?action=read&count=20">Dictionary Examples - Read (20)</a></div>
    <div><a target="_blank" href="/definitions/read-examples?action=read&count=100">Dictionary Examples - Read (100)</a></div>
    <div><a target="_blank" href="/definitions/read-examples?action=read&count=37656">Dictionary Examples - Read (All)</a></div>
    <hr/>
    <div><a target="_blank" href="/definitions/review-top-20-verbs">Top 20 Verbs - List</a></div>
    <div><a target="_blank" href="/definitions/review-top-20-verbs/quiz">Top 20 Verbs - Quiz</a></div>
    <div><a target="_blank" href="/definitions/review-top-20-verbs/flashcards">Top 20 Verbs - Flashcards</a></div>
    <div><a target="_blank" href="/definitions/review-top-20-verbs/reader">Top 20 Verbs - Reader</a></div>
    <hr/>
    <div><a target="_blank" href="/definitions/review-newest">20 Newest Words - List</a></div>
    <div><a target="_blank" href="/definitions/review-newest/quiz">20 Newest Words - Quiz</a></div>
    <div><a target="_blank" href="/definitions/review-newest/flashcards">20 Newest Words - Flashcards</a></div>
    <div><a target="_blank" href="/definitions/review-newest/reader">20 Newest Words - Reader</a></div>
    <hr/>
    <div><a target="_blank" href="/definitions/review-newest-verbs">20 Newest Verbs - List</a></div>
    <div><a target="_blank" href="/definitions/review-newest-verbs/quiz">20 Newest Verbs - Quiz</a></div>
    <div><a target="_blank" href="/definitions/review-newest-verbs/flashcards">20 Newest Verbs - Flashcards</a></div>
    <div><a target="_blank" href="/definitions/review-newest-verbs/reader">20 Newest Verbs - Reader</a></div>
    <hr/>
    <div><a target="_blank" href="/definitions/review-random-words">20 Random Words - List</a></div>
    <div><a target="_blank" href="/definitions/review-random-words/quiz">20 Random Words - Quiz</a></div>
    <div><a target="_blank" href="/definitions/review-random-words/flashcards">20 Random Words - Flashcards</a></div>
    <div><a target="_blank" href="/definitions/review-random-words/reader">20 Random Words - Reader</a></div>
    <hr/>
    <div><a target="_blank" href="/definitions/review-random-verbs">20 Random Verbs - List</a></div>
    <div><a target="_blank" href="/definitions/review-random-verbs/quiz">20 Random Verbs - Quiz</a></div>
    <div><a target="_blank" href="/definitions/review-random-verbs/flashcards">20 Random Verbs - Flashcards</a></div>
    <div><a target="_blank" href="/definitions/review-random-verbs/reader">20 Random Verbs - Reader</a></div>
    <hr/>
    <div><a target="_blank" href="/definitions/list-tag/8">Hot List 1</a></div>
    <div><a target="_blank" href="/definitions/review/8">Hot List 1 - Quiz</a></div>
    <div><a target="_blank" href="/definitions/review/8/1">Hot List 1 - Flashcards</a></div>
    <div><a target="_blank" href="/definitions/read-list/8">Hot List 1 - Read</a></div>
    <hr/>

    <h3 class="{{$classH3}}">Practice Text</h3>
    <div><a target="_blank" href="/practice">Practice Text</a></div>
    <div><a target="_blank" href="/snippets/read">Snippets Read</a></div>
    <div><a target="_blank" href="/snippets/read?count=5">Snippets Read (5)</a></div>
    <div><a target="_blank" href="/snippets/review/flashcards">Snippets Flashcards</a></div>
    <div><a target="_blank" href="/snippets/review/flashcards/20">Snippets Flashcards (20)</a></div>
    <hr/>

    @if (false)
    <h3 class="{{$classH3}}"></h3>
    <div><a target="_blank" href=""></a></div>
    <div><a target="_blank" href=""></a></div>
    <div><a target="_blank" href=""></a></div>
    <hr/>
    @endif

</div>
@endsection


