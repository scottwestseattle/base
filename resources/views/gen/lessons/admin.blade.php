@php
    $prefix = 'lessons';
    $isAdmin = isAdmin();
@endphp
@extends('layouts.app')
@section('title', trans_choice('proj.Lesson', 2))
@section('menu-submenu')@component('gen.lessons.menu-submenu', ['prefix' => $prefix])@endcomponent @endsection
@section('content')

<h1>{{trans_choice('proj.Lesson', 2)}} ({{count($records)}})</h1>

<table class="table table-responsive">
    <thead>
        <tr>
            <th></th><th></th><th>{{trans_choice('proj.Course', 2)}}</th><th>@LANG('ui.Title')</th><th>@LANG('ui.Description')</th><th></th><th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($records as $record)
        <tr>
            <td><a href="/{{$prefix}}/edit/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-edit"></span></a></td>
            <td><a href="/{{$prefix}}/publish/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-publish"></span></a></td>

            <td><a href="/courses/view/{{$record->parent_id}}">{{$record->parent_id}}</a></td>
            <td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->lesson_number}}.{{$record->section_number}}&nbsp;{{$record->title}}</a></td>
            <td class="">{{substr($record->description, 0, 200)}}</td>
            <td>
                @if ($record->isUnfinished())
                <a href="/{{$prefix}}/publish/{{$record->id}}"><button type="button" class="btn btn-xs {{$record->getStatus()['btn']}}">{{$record->getStatus()['text']}}</button></a>
                @endif
            </td>
            <td><a href="/{{$prefix}}/confirmdelete/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-delete"></span></a></td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection
