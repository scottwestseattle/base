@php
    $showPrivate = Auth::check() && isset($options['private']['records']) && count($options['private']['records']) > 0;
    $showOther = isAdmin() && isset($options['other']['records']) && count($options['other']['records']) > 0;
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

    $orderBy = isset($options['orderBy']) ? $options['orderBy'] : 'default';
@endphp
@extends('layouts.app')
@section('title', trans_choice('proj.Article', 2) )
@section('menu-submenu')@component('gen.articles.menu-submenu', ['prefix' => 'articles'])@endcomponent @endsection
@section('content')

@if ($showPrivate || $showOther)
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link nav-link-tab {{$active1}}" onclick="ajaxexec('{{$setSessionUrl . 1}}');" id="articles-tab" data-toggle="tab" href="#articles" role="tab" aria-controls="articles" aria-selected="true">
                {{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{$options['public']['count']}})</span>
            </a>
        </li>
        @if ($showPrivate)
        <li class="nav-item" role="presentation">
            <a class="nav-link nav-link-tab {{$active2}}" onclick="ajaxexec('{{$setSessionUrl . 2}}')" id="private-tab" data-toggle="tab" href="#private" role="tab" aria-controls="private" aria-selected="false">
                @LANG('ui.Private')&nbsp;<span style="font-size:.8em;">({{$options['private']['count']}})</span>
            </a>
        </li>
        @endif
        @if ($showOther)
        <li class="nav-item" role="presentation">
            <a class="nav-link nav-link-tab {{$active3}}" onclick="ajaxexec('{{$setSessionUrl . 3}}')" id="others-tab" data-toggle="tab" href="#others" role="tab" aria-controls="others" aria-selected="false">
                @LANG('proj.Other')&nbsp;<span style="font-size:.8em;">({{$options['other']['count']}})</span>
            </a>
        </li>
        @endif
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade {{$active1}}" id="articles" role="tabpanel" aria-labelledby="articles-tab">
            @component('shared.articles', ['records' => $options['public']['records'], 'release' => 'public'])@endcomponent
        </div>

    @if ($showPrivate)
        <div class="tab-pane fade {{$active2}}" id="private" role="tabpanel" aria-labelledby="private-tab">
            @component('shared.articles', ['records' => $options['private']['records'], 'release' => 'private'])@endcomponent
        </div>
    @endif

    @if ($showOther)
        <div class="tab-pane fade {{$active3}}" id="others" role="tabpanel" aria-labelledby="others-tab">
            @component('shared.articles', ['records' => $options['other']['records'], 'release' => 'other'])@endcomponent
        </div>
    @endif
@else
    <h3 class="">{{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{$options['public']['count']}})</span></h3>
    @component('shared.articles', ['records' => $options['public']['records'], 'release' => 'public', 'orderBy' => $orderBy])@endcomponent
@endif

@endsection
