@extends('layouts.app')
@section('title', __('proj.Search'))
@section('content')

<h1>@LANG('ui.Search'){{$isPost ? ' (' . $count . ')' : ''}}</h1>

<form method="POST" action="/search">
    <div class="form-group form-control-big">
        <input type="text" id="searchText" name="searchText" class="form-control" value="{{$search}}" autofocus/>
    </div>
    <div class="form-group">
            <button type="submit" name="submit" class="btn btn-primary">@LANG('ui.Search')</button>
    </div>
    {{ csrf_field() }}
</form>

@if ($count > 0)
        <table class="table table-striped">
            <tbody>

            @if (isset($entries))
                @foreach($entries as $record)
                    <tr>
                        <td>{{trans_choice('proj.' . $record->getTypeFlagName($record->type_flag), 2)}}</td>
                        <td><a href="/articles/view/{{$record->permalink}}" target="_blank">{{$record->title}}</a></td>
                    </tr>
                @endforeach
            @endif

            @if (isset($snippets))
                @foreach($snippets as $record)
                    <tr>
                        <td>@LANG('proj.Practice Text')</td>
                        <td><a href="/practice/{{$record->id}}" target="_blank">{{$record->examples}}</a></td>
                    </tr>
                @endforeach
            @endif

            @if (isset($definitions))
                @foreach($definitions as $record)
                    <tr>
                        <td>@LANG('proj.Dictionary')</td>
                        <td><a href="/definitions/view/{{$record->permalink}}" target="_blank">{{$record->title}}</a></td>
                    </tr>
                @endforeach
            @endif

            @if (isset($lessons))
                @foreach($lessons as $record)
                    <tr>
                        <td>{{trans_choice('proj.Lesson', 2)}}</td>
                        <td><a href="/lessons/view/{{$record->id}}" target="_blank">{{$record->courseTitle}} - {{$record->lesson_number}}.{{$record->section_number}} {{$record->title}}</a></td>
                    </tr>
                @endforeach
            @endif

            @if (isset($words))
                @foreach($words as $record)
                    <tr>
                        @if ($record->isLessonWord())
                            <td>@LANG('content.Lesson')</td>
                            <td><a href="/lessons/view/{{$record->parent_id}}" target="_blank">{{$record->courseTitle}} - {{$record->lesson_number}}.{{$record->section_number}} {{$record->lessonTitle}}</a></td>
                        @elseif ($record->isVocabListWord())
                            <td>@LANG('content.Vocabulary')</td>
                            <td>
                                <a href="/words/view/{{$record->id}}" target="_blank">{{$record->title}}</a>
                                &nbsp;(<a href="/vocab-lists/view/{{$record->vocab_list_id}}" target="_blank">@LANG('content.List')</a>)
                            </td>
                        @else
                            <td>@LANG('content.Vocabulary')</td>
                            <td><a href="/words/view/{{$record->id}}" target="_blank">{{$record->title}}</a></td>
                        @endif
                    </tr>
                @endforeach
            @endif

            </tbody>
        </table>
    </div>
@endif

@endsection
