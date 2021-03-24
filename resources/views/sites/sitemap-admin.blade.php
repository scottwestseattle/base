@extends('layouts.app')
@section('title', trans_choice('ui.List', 2))
@section('menu-submenu')@component('tags.menu-submenu')@endcomponent @endsection
@section('content')
@php
    $count = count($siteMaps);
@endphp

<h1>Site Maps ({{count($siteMaps)}})</h1>

<div class="form-control-big">

    @foreach ($siteMaps as $siteMap)
        <h3>{{count($siteMap['sitemap'])}} URLs written to: {{$siteMap['filename']}}</h3>
    @endforeach

    @if ($count > 1)
        <h1>Details</h1>
    @endif

    @foreach ($siteMaps as $siteMap)

    @if ($count > 1)
        <h3>{{count($siteMap['sitemap'])}} URLs written to: {{$siteMap['filename']}}</h3>
    @endif

    <table class="table" style="display:default;">

        @if (isset($siteMap['sitemap']))
            @foreach ($siteMap['sitemap'] as $record)
            <tr>
                <td><a target="_blank" href="{{$record}}">{{$record}}</a></td>
            </tr>
            @endforeach
        @endif

    </table>

    @endforeach

</div>

@endsection
