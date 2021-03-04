@php
    $showTabs = Auth::check() && isset($options['private']) && count($options['private']) > 0;
@endphp
@extends('layouts.app')
@section('title', trans_choice('proj.Article', 2) )
@section('menu-submenu')@component('gen.articles.menu-submenu', ['prefix' => 'articles'])@endcomponent @endsection
@section('content')

@if ($showTabs)
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a id="nav-link-tab1" class="nav-link active" href="#" onclick="setTab(event, 1);">
            <span class="nav-link-tab">
                {{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{count($options['public'])}})</span>
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a id="nav-link-tab2" class="nav-link" href="#" onclick="setTab(event, 2);">
            <span class="nav-link-tab">
                @LANG('ui.Private')&nbsp;<span style="font-size:.8em;">({{count($options['private'])}})</span>
            </span>
        </a>
    </li>
</ul>
@else
<h3 class="">
    {{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{count($options['public'])}})</span>
</h3>
@endif

<div style="" id="tab-tab1">
    @component('shared.articles', ['records' => $options['public'], 'release' => 'public'])@endcomponent
</div>
@if ($showTabs)
<div style="display:none" id="tab-tab2">
    @component('shared.articles', ['records' => $options['private'], 'release' => 'private'])@endcomponent
</div>
@endif


@endsection

@if (false)
<h3 class="mt-2 nodec">@LANG($title)
    <span style="font-size:.8em;">({{count($options['public'])}})</span>
    @if (isAdmin())
        <a href="/articles/add"><span>+</span></a>
    @endif
    </span>
</h3>
@endif
