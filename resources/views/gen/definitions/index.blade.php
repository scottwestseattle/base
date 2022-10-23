@extends('layouts.app')
@section('title', trans_choice('proj.Definition', 2))
@section('menu-submenu')@component('gen.definitions.menu-submenu')@endcomponent @endsection
@section('content')

<h1>{{trans_choice('proj.Definition', 2)}} ({{count($records)}})</h1>
<table class="table table-responsive table-fat">
    <thead>
        <tr>
            <th></th><th>@LANG('ui.Title')</th><th>{{__('ui.Type')}} / {{trans_choice('ui.User', 1)}}</th><th>{{trans_choice('proj.Definition', 1)}}</th><th>@LANG('ui.Created')</th>
            @if (isAdmin())
            <th></th>
            @endif
        </tr>
    </thead>
    <tbody>
    @foreach($records as $record)
        <tr>
            @if ($record->isSnippet())
                <td class="icon"><a href='/definitions/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
                <td><a href="/definitions/view/{{$record->permalink}}">{{Str::limit($record->title, 30)}} ({{$record->id}})</a></td>
            @else
                <td class="icon"><a href='/definitions/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
                <td><a href="/definitions/{{ blank($record->permalink) ? 'view/' . $record->id : $record->permalink }}">{{$record->title}}</a></td>
            @endif
            <td class="">{{$record->type_flag}} / {{isset($record->user_id) ? $record->user_id : 'null'}}</td>
            @if (isset($record->definition))
                <td>{{Str::limit($record->definition, 200)}}</td>
            @elseif (isset($record->translation_en))
                <td>{{Str::limit($record->translation_en, 200)}}</td>
            @elseif (isset($record->examples))
                <td>{{Str::limit($record->examples, 200)}}</td>
            @else
                <td></td>
            @endif
            <td class="date-sm">{{$record->created_at}}</td>
            @if (isAdmin())
                <td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/definitions/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>

@endsection
