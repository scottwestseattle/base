@php
    $showPrivate = Auth::check() && isset($options['private']) && count($options['private']) > 0;
    $showOther = isAdmin() && isset($options['other']) && count($options['other']) > 0;
    $setSessionUrl = '/set-session?tag=articlesTab&value=';

    // active tab comes from session
    $activeTab = isset($options['activeTab']) ? $options['activeTab'] : 1;

    $active1 = 'show active'; //default
    $active2 = '';
    $active3 = '';

    if ($showPrivate && $activeTab == 2)
    {
        $active2 = 'show active';
        $active1 = '';
    }
    else if ($showOther && $activeTab == 3)
    {
        $active3 = 'show active';
        $active1 = '';
    }

@endphp
@extends('layouts.app')
@section('title', trans_choice('proj.Article', 2) )
@section('menu-submenu')@component('gen.articles.menu-submenu', ['prefix' => 'articles'])@endcomponent @endsection
@section('content')

@if ($showPrivate || $showOther)
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link nav-link-tab {{$active1}}" onclick="ajaxexec('{{$setSessionUrl . 1}}');" id="articles-tab" data-toggle="tab" href="#articles" role="tab" aria-controls="articles" aria-selected="true">
                {{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{count($options['public'])}})</span>
            </a>
        </li>
        @if ($showPrivate)
        <li class="nav-item" role="presentation">
            <a class="nav-link nav-link-tab {{$active2}}" onclick="ajaxexec('{{$setSessionUrl . 2}}')" id="private-tab" data-toggle="tab" href="#private" role="tab" aria-controls="private" aria-selected="false">
                @LANG('ui.Private')&nbsp;<span style="font-size:.8em;">({{count($options['private'])}})</span>
            </a>
        </li>
        @endif
        @if ($showOther)
        <li class="nav-item" role="presentation">
            <a class="nav-link nav-link-tab {{$active3}}" onclick="ajaxexec('{{$setSessionUrl . 3}}')" id="others-tab" data-toggle="tab" href="#others" role="tab" aria-controls="others" aria-selected="false">
                @LANG('proj.Other')&nbsp;<span style="font-size:.8em;">({{count($options['other'])}})</span>
            </a>
        </li>
        @endif
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade {{$active1}}" id="articles" role="tabpanel" aria-labelledby="articles-tab">
            @component('shared.articles', ['records' => $options['public'], 'release' => 'public'])@endcomponent
        </div>

    @if ($showPrivate)
        <div class="tab-pane fade {{$active2}}" id="private" role="tabpanel" aria-labelledby="private-tab">
            @component('shared.articles', ['records' => $options['private'], 'release' => 'private'])@endcomponent
        </div>
    @endif

    @if ($showOther)
        <div class="tab-pane fade {{$active3}}" id="others" role="tabpanel" aria-labelledby="others-tab">
            @component('shared.articles', ['records' => $options['other'], 'release' => 'other'])@endcomponent
        </div>
    @endif
@else
    <h3 class="">{{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{count($options['public'])}})</span></h3>
    @component('shared.articles', ['records' => $options['public'], 'release' => 'public'])@endcomponent
@endif

@endsection
