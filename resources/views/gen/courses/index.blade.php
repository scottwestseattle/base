@extends('layouts.app')
@section('title', trans_choice('proj.Course', 2) )
@section('menu-submenu')@component('gen.courses.menu-submenu', ['prefix' => 'courses'])@endcomponent @endsection
@section('content')

<h1>{{trans_choice('proj.Course', 2)}} ({{count($public)}})</h1>

<div class="row row-course">
    @foreach($public as $record)
    <div class="col-sm-12 col-lg-6 col-course"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
        <div class="card card-course {{$record->getCardColor()}} truncate">
            <a href="/courses/view/{{$record->id}}">
                <div class="card-header">
                    <div>{{$record->title}}</div>
                    @component('components.data-sitename', ['isAdmin' => isAdmin(), 'siteId' => $record->site_id])@endcomponent
                </div>
                <div class="card-body">
                    <p class="card-text">{{$record->description}}</p>
                </div>
            </a>
        </div>
    </div>
    @endforeach
</div>

@if (isAdmin())
<h1>@LANG('proj.Courses Under Development') ({{count($private)}})</h1>

<div class="row row-course">
    @foreach($private as $record)
    <div class="col-sm-12 col-lg-6 col-course"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
        <div class="card card-course {{$record->getCardColor()}} truncate">
        <a href="/courses/view/{{$record->id}}">
            <div class="card-header">
                <div>{{$record->title}}</div>
                @component('components.data-sitename', ['isAdmin' => isAdmin(), 'siteId' => $record->site_id])@endcomponent
            </div>
            <div class="card-body"><p class="card-text">{{$record->description}}</p></div>
        </a>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
