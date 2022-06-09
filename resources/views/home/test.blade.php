@extends('layouts.app')
@section('content')
@php
@endphp
<div class="container page-normal">

    <h1>@LANG('ui.Test')</h1>

    <h2 class="">Global</h2>
    <div><a target="_blank" href="/events">{{trans_choice('base.Event', 2)}}</a></div>
    <div><a target="_blank" href="/users">Users</a></div>
    <div><a target="_blank" href="/hash">Hash</a></div>
    <hr/>

    <h2 class="">{{__('Ajax Links')}}</h2>
    <div><a target="_blank" href="/set-session?tag=articlesTab&value=2">Set Article Tab = 2</a></div>
    <div><a target="_blank" href="/set-session?tag=articlesTab&value=3">Set Article Tab = 3</a></div>
    <div><a target="_blank" href="/definitions/scrape-definition/tener">Scrape Definition</a></div>
    <div><a target="_blank" href="/definitions/conjugationsgenajax/tener">Scrape Conjugation</a></div>
    <div><a target="_blank" href="/definitions/find/tener">Find Word</a></div>
    <div><a target="_blank" href="/history/add-public?programName=Program 1&programId=1&sessionName=Session 2&sessionId=2&count=3&score=4&seconds=5&extra=6&route=practice-text">Add History</a></div>
    <hr />

    <h2 class="">{{__('RSS Links')}}</h2>
    <div><a target="_blank" href="/courses/rss">Courses</a></div>
    <div><a target="_blank" href="/lessons/rss">Lessons</a></div>
    <div><a target="_blank" href="/history/rss">History</a></div>
    <hr />

    @if (false)
    <h2 class="">{{__('')}}</h2>
    <div><a target="_blank" href=""></a></div>
    <div><a target="_blank" href=""></a></div>
    <div><a target="_blank" href=""></a></div>
    <hr/>
    @endif

</div>
@endsection


