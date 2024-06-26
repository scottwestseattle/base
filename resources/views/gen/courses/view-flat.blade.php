@php
    $prefix = 'courses';
    $toggleLessons = false;
    $locale = app()->getLocale();
@endphp
@extends('layouts.app')
@section('title', trans_choice('proj.Course', 2) )
@section('menu-submenu')@component('gen.courses.menu-submenu', ['prefix' => 'courses', 'record' => $record])@endcomponent @endsection
@section('content')
<div class="page-nav-buttons">
    <a class="btn btn-success btn-md" role="button" href="{{route('courses', ['locale' => $locale])}}">@LANG('proj.Back to Course List')
    <span class="glyphicon glyphicon-button-back-to"></span>
    </a>
</div>

<h3 name="title" class="">{{$record->title }}
    @if (isAdmin())
        @if (!App\Status::isFinished($record->wip_flag))
            <a class="btn {{($wip=\App\Status::getWipStatus($record->wip_flag))['btn']}} btn-xs" role="button" href="{{route('courses.publish', ['locale' => $locale, 'course' => $record->id])}}">{{__($wip['text'])}}</a>
        @endif
        @if (!App\Status::isPublic($record->release_flag))
            <a class="btn {{($release=App\Status::getReleaseStatus($record->release_flag))['btn']}} btn-xs" role="button" href="{{route('courses.publish', ['locale' => $locale, 'course' => $record->id])}}">{{__($release['text'])}}</a>
        @endif
    @endif
</h3>

<p>{{$record->description}}</p>

<a href="{{route('lessons.view', ['locale' => $locale, 'lesson' => $firstId])}}">
    <button type="button" style="text-align:center; font-size: 1.3em; color:white;" class="btn btn-info btn-lesson-index" {{$disabled}}>@LANG('proj.Start at the beginning')</button>
</a>

<h1 class="mt-1 mb-4">{{trans_choice('proj.Lesson', 2)}} ({{$displayCount}})
    @if (isAdmin())
        <span><a href="{{route('lessons.admin', ['locale' => $locale, 'lesson' => $record->id])}}"><span class="glyphicon glyphicon-cog glyphCustom"></span></a></span>
        <span><a href="{{route('lessons.add', ['locale' => $locale, 'lesson' => $record->id])}}"><span class="glyphicon glyphicon-plus-sign glyphCustom"></span></a></span>
    @endif
</h1>

<div>
@if (isset($records))
    @foreach($records as $record)
    <div class="drop-box-ghost mb-4" style="padding:10px 10px 20px 15px;">
        <div style="font-size:1.3em; font-weight:normal;">
            <a href="" onclick="event.preventDefault(); $('#parts{{$record[0]->lesson_number}}').toggle();">{{trans_choice('proj.Lesson', 1)}}&nbsp;{{$record[0]->lesson_number}}:&nbsp;{{isset($record[0]->title_chapter) ? $record[0]->title_chapter : $record[0]->title}}</a>
            <div id="parts{{$record[0]->lesson_number}}" class="mt-2 {{$chapterCount > 1 && $toggleLessons ? 'hidden' : ''}}">
            @foreach($record as $r)
            <div class="ml-2 mt-1" style="font-size:14px;">
                <div class="">
                    <a href="{{route('lessons.view', ['locale' => $locale, 'lesson' => $r->id])}}">{{$record[0]->lesson_number}}.{{$r->section_number}} {{$r->title}}</a>
                </div>
            </div>
            @endforeach
            </div>

        </div>
    </div>
    @endforeach
@endif
</div>


@endsection
