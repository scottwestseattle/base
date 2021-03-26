@extends('layouts.app')
@section('title', __('ui.Site Map'))
@section('content')
@php
    $count = count($siteMaps);
@endphp

@if ($count > 1)
    <h1>Site Maps ({{$count}})</h1>
@else
    <h1>{{__('ui.Site Map')}}</h1>
@endif

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

        @if (isset($siteMap['sitemap']))
            @foreach ($siteMap['sitemap'] as $record)
                <div class="small-thin-text mb-2"><a target="_blank" href="{{$record}}">{{$record}}</a></div>
            @endforeach
        @endif

    @endforeach

</div>

@endsection
