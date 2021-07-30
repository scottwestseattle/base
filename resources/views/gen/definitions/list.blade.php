@php
    $name = isset($name) ? $name : (isset($tag) ? $tag->name : 'name not set');
    $lengthLimit = 125;
@endphp
@extends('layouts.app')
@section('title', trans_choice('ui.List', 2))
@section('menu-submenu')@component('tags.menu-submenu')@endcomponent @endsection
@section('content')

<div class="page-nav-buttons">
    <a class="btn btn-success btn-sm btn-nav-top" role="button" href="/favorites">
        @LANG('proj.Back to Lists')<span class="glyphicon glyphicon-button-back-to"></span>
    </a>
    @if (isset($tag))
        <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="/definitions/review/{{$tag->id}}">
            @LANG('ui.Review')&nbsp;<span class="glyphicon glyphicon-eye-open"></span>
        </a>
        <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="/definitions/review/{{$tag->id}}/1">
            @LANG('proj.Flashcards')<span class="ml-1 glyphicon glyphicon-flash"></span>
        </a>
        <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="/definitions/read-list/{{$tag->id}}">
            @LANG('proj.Reader')<span class="ml-1 glyphicon glyphicon-volume-up"></span>
        </a>
    @else
        <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="{{$_SERVER['REQUEST_URI']}}/quiz">
            @LANG('ui.Review')&nbsp;<span class="glyphicon glyphicon-eye-open"></span>
        </a>
        <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="{{$_SERVER['REQUEST_URI']}}/flashcards">
            @LANG('proj.Flashcards')<span class="ml-1 glyphicon glyphicon-flash"></span>
        </a>
    @endif
</div>

<h3 name="" class="" style="margin-bottom:10px;">{{$name}}@component('components.badge', ['text' => count($records)])@endcomponent</h3>
<div id="removeStatus"></div>

<table style="width:100%;" class="table">
    <tbody>
    @foreach($records as $record)
        <tr id="row{{$record->id}}">
            @if (isAdmin())
                <td class="" style=""><a href="/{{$record->isSnippet() ? 'practice' : 'definitions'}}/edit/{{$record->id}}">@component('components.icon-edit')@endcomponent</a></td>
            @endif
            <td style="width:100%;">
                @if ($record->isSnippet())
                    <a href="/definitions/view/{{$record->permalink}}">{{Str::limit($record->title_long, $lengthLimit)}}</a>
                    <div>
                        @if (isset($record->translation_en))
                            <div class="medium-thin-text" >{{Str::limit($record->translation_en, $lengthLimit)}}</div>
                        @else
                            <a class="small-thin-text red" href="/practice/edit/{{$record->id}}">@LANG('proj.Add Translation')</a>
                        @endif
                    </div>
                @else
                    <a href="/definitions/view/{{$record->permalink}}">{{$record->title}}</a>
                    <div>
                        @if (isset($record->translation_en))
                            <div class="medium-thin-text" >{{$record->translation_en}}</div>
                        @else
                            <a class="small-thin-text red" href="/definitions/edit/{{$record->id}}">@LANG('proj.Add Translation')</a>
                        @endif
                    </div>
                @endif

                @if (isAdmin())
                    <div class="small-thin-text" style="">{{$record->updated_at}}</div>
                @endif
            </td>

            @if (isset($tag))
            @if (false && count($lists) > 1)
            <td class="icon mr-2">
                <div class="dropdown" >
                    <!-- removed 'dropdown-toggle' class to remove the down arrow graphic -->
                    <a class="" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"	href="" onclick="">
                        <div class="glyphCustom-md glyphicon glyphicon-heart"></div>
                    </a>

                    <ul class="small-thin-text dropdown-menu dropdown-menu-right">
                        @foreach($lists as $list)
                            @if ($tag->id != $list->id)
                                <li><a class="dropdown-item" href="/definitions/set-favorite-list/{{$record->id}}/{{$tag->id}}/{{$list->id}}">{{$list->name}}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </td>
            @endif

            <td class="icon mr-3">
                <div class="dropdown" >
                    <a class="" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"	href="" onclick="">
                        <div class="glyphCustom-md glyphicon glyphicon-remove"></div>
                    </a>

                    <ul class="small-thin-text dropdown-menu dropdown-menu-right">
                        <li><a class="dropdown-item" href="/definitions/set-favorite-list/{{$record->id}}/{{$tag->id}}/0">@LANG('proj.Remove from List')</a></li>
                    </ul>
                </div>
            </td>
            @endif

        </tr>
    @endforeach
    </tbody>
</table>

@endsection
