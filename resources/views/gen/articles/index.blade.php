@php
    $showPrivate = Auth::check() && isset($options['private']) && count($options['private']) > 0;
    $showOther = isAdmin() && isset($options['other']) && count($options['other']) > 0;
@endphp
@extends('layouts.app')
@section('title', trans_choice('proj.Article', 2) )
@section('menu-submenu')@component('gen.articles.menu-submenu', ['prefix' => 'articles'])@endcomponent @endsection
@section('content')

@if ($showPrivate || $showOther)
<ul class="nav nav-tabs">

    <li class="nav-item">
        <a id="nav-link-tab1" class="nav-link active" href="#" onclick="setTab(event, 1);">
            <span class="nav-link-tab">
                {{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{count($options['public'])}})</span>
            </span>
        </a>
    </li>

    @if ($showPrivate)
    <li class="nav-item">
        <a id="nav-link-tab2" class="nav-link" href="#" onclick="setTab(event, 2);">
            <span class="nav-link-tab">
                @LANG('ui.Private')&nbsp;<span style="font-size:.8em;">({{count($options['private'])}})</span>
            </span>
        </a>
    </li>
    @endif

    @if ($showOther)
    <li class="nav-item">
        <a id="nav-link-tab3" class="nav-link" href="#" onclick="setTab(event, 3);">
            <span class="nav-link-tab">
                @LANG('proj.Other')&nbsp;<span style="font-size:.8em;">({{count($options['other'])}})</span>
            </span>
        </a>
    </li>
    @endif
</ul>
@else
<h3 class="">
    {{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{count($options['public'])}})</span>
</h3>
@endif

<div style="" id="tab-tab1">
    @component('shared.articles', ['records' => $options['public'], 'release' => 'public'])@endcomponent
</div>

@if ($showPrivate)
    <div style="display:none" id="tab-tab2">
        @component('shared.articles', ['records' => $options['private'], 'release' => 'private'])@endcomponent
    </div>
@endif

@if ($showOther)
    <div style="display:none" id="tab-tab3">
        @component('shared.articles', ['records' => $options['other'], 'release' => 'other'])@endcomponent
    </div>
@endif

@endsection
