@extends('layouts.app')
@section('title', trans_choice('ui.List', 2))
@section('menu-submenu')@component('tags.menu-submenu')@endcomponent @endsection
@section('content')

<div class="page-nav-buttons">
    <a class="btn btn-success btn-sm btn-nav-top" role="button" href="/favorites">
        @LANG('proj.Back to Lists')<span class="glyphicon glyphicon-button-back-to"></span>
    </a>
    <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="/definitions/review/{{$tag->id}}">
        @LANG('ui.Review')&nbsp;<span class="glyphicon glyphicon-eye-open"></span>
    </a>
    <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="/definitions/review/{{$tag->id}}/1">
        @LANG('proj.Flashcards')<span class="ml-1 glyphicon glyphicon-flash"></span>
    </a>
</div>

<h3 name="" class="" style="margin-bottom:10px;">{{$tag->name}}@component('components.badge', ['text' => count($records)])@endcomponent</h3>
<div id="removeStatus"></div>

<table style="width:100%;" class="table xtable-striped">
    <tbody>
    @foreach($records as $record)
        <tr id="row{{$record->id}}">
            <td style="width:100%;">
                <a href="/definitions/show/{{$record->id}}">{{$record->title}}</a>
                @if (isset($record->translation_en))
                    <div>{{$record->translation_en}}</div>
                @else
                    <div>{{$record->examples}}</div>
                @endif
            </td>

            <td>{{$record->updated_at}}</td>

            @if (count($lists) > 1)
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
                        <li><a class="dropdown-item" href="" onclick="unheartDefinition(event, {{$record->id}}, '#removeStatus'); $('#row{{$record->id}}').hide();">@LANG('proj.Remove from List')</a></li>
                    </ul>
                </div>
            </td>

        </tr>
    @endforeach
    </tbody>
</table>

@endsection
