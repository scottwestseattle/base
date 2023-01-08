@extends('layouts.app')
@section('title', trans_choice('ui.History', 2))
@section('menu-submenu')@component('gen.history.menu-submenu')@endcomponent @endsection
@section('content')
@php
    $records = isset($history['records']) ? $history['records'] : [];
@endphp
<div>
    <h1 class="">{{trans_choice('ui.History', 2)}}<span class="title-count">({{count($records)}})</span></h1>
    <table class="table table-striped">
    @foreach ($records as $record)
        @php
            $bg = App\DateTimeEx::getDayColor($record->created_at);
            $info = $record->getInfo();
        @endphp
        <tr class="mb-3" style="">
            <td>
                @if ($info['hasUrl'])
                    <div><a class="" href="{{$info['url']}}">{{$info['subTypeName']}}: {{$info['programName']}}</a> ({{$info['stats']}})</div>
                @else
                    <div>{{$info['subTypeName']}}: {{$info['programName']}} ({{$info['stats']}})</div>
                @endif

                <div class="small-thin-text">Actual: {{$record->created_at}}</div>
                <div class="small-thin-text">Local: {{App\DateTimeEx::getLocalDateTimeString($record->created_at)}}</div>

            </td>
            <td class="small-thin-text" style="">
                Program Id: {{$record->program_id}},
                Type: {{$record->type_flag}},
                Sub: {{$record->subtype_flag}},
                Route: {{$record->route}},
                Session: {{$record->session_name}},
                Session Id: ({{$record->session_id}})
                @if ($record->count > 0)
                    @LANG('ui.Count'): {{$record->count}},
                @endif
                @if ($record->seconds > 0)
                    {{trans_choice('ui.Second', 2)}}: {{$record->seconds}},
                @endif
                @if ($record->score > 0)
                    @LANG('proj.Score'): {{$record->score}},
                @endif
                @if ($record->extra > 0)
                    @LANG('proj.Extra'): {{$record->extra}},
                @endif
            </td>
            <td style="width:10px;"><a class="medium-thin-text" href="/history/edit/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-edit"></span></a></td>
            <td style="width:10px;" class="steel-blue">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/history/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
        </tr>
    @endforeach
    </table>
</div>

@endsection
