@extends('layouts.app')
@section('title', trans_choice('proj.Story', 2))
@if (false)
    @section('menu-submenu')@component('gen.articles.menu-submenu', ['index' => 'articles', 'isIndex' => true])@endcomponent @endsection
@endif
@section('content')
@php
    $locale = app()->getLocale();

    $colors = [
        'Tomato',
        'MediumSeaGreen',
        'DodgerBlue',
        'Teal',
        'purple',
        'maroon',
        'orange',
        'Violet',
        'SlateBlue',
        'Olive',
        'DarkOrange',
    ];

    $colorIndex = 0;
    $imgPath = public_path() . '/img/backgrounds/covers';
@endphp
<div class="container page-normal">

	<h1>{{trans_choice('proj.Story', 2)}}<span class="title-count">({{count($records)}})</span></h1>

    <div class="row mb-3">
        @foreach($records as $record)
            @php $photo = file_exists($imgPath . '/' . $record->id . '.png'); @endphp
            <div class="text-center mb-2 ml-2"
            style="min-width:100px; max-width:45%; border-radius:10px; background-color: {{$photo ? 'default' : $colors[$colorIndex % 10]}};
                background-image:url('/img/books/pattern.png'); background-size:cover;">
                @if (false)
                <a href="{{route('articles.view', ['locale' => $locale, 'permalink' => $record->permalink])}}">
                @else
                <a href="{{route('articles.read', ['locale' => $locale, 'entry' => $record->id])}}">
                @endif
                    @if ($photo)
                        <img style="height:230px;" src="/img/backgrounds/covers/{{$record->id}}.png" />
                    @else
                        <div style="height:230px; width:151px;">
                            <div style="color: white; padding: 30% 20px; overflow-wrap:break-word; font-weight:bold; font-size:20px;">
                                {{$record->title}}
                            </div>
                        </div>
                        @php $colorIndex++ @endphp
                    @endif
                </a>
            </div>
        @endforeach
    </div>
</div>

@endsection
