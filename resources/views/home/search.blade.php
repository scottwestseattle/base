@extends('layouts.app')
@section('title', __('ui.Search'))
@section('content')
@php
    $articlesChecked = (isset($options['articles']) && $options['articles']) ? 'CHECKED' : '';
    $dictionaryChecked = (isset($options['dictionary']) && $options['dictionary']) ? 'CHECKED' : '';
    $snippetsChecked = (isset($options['snippets']) && $options['snippets']) ? 'CHECKED' : '';
    $wordChecked = (isset($options['word']) && $options['word']) ? 'CHECKED' : '';
@endphp
<h1>@LANG('ui.Search'){{$isPost ? ' (' . $count . ')' : ''}}</h1>

<form method="POST" action="/search">
    <div class="form-group form-control-big">
        <input type="text" id="searchText" name="searchText" class="form-control" value="{{$search}}" autofocus/>
        <div class="mt-3">

            <div>
            <input type="checkbox" name="articles_flag" id="articles_flag" class="ml-2" {{$articlesChecked}}/>
            <label for="articles_flag" class="checkbox-big-label">{{trans_choice('proj.Article', 2)}}</label>
            </div>

            <div>
            <input type="checkbox" name="dictionary_flag" id="dictionary_flag" class="ml-2" {{$dictionaryChecked}} />
            <label for="dictionary_flag" class="checkbox-big-label">@LANG('proj.Dictionary')</label>
            </div>

            <div>
            <input type="checkbox" name="snippets_flag" id="snippets_flag" class="ml-2" {{$snippetsChecked}} />
            <label for="snippets_flag" class="checkbox-big-label">@LANG('proj.Practice Text')</label>
            </div>

            <div>
            <input type="checkbox" name="word_flag" id="word_flag" class="ml-2" {{$wordChecked}} />
            <label for="word_flag" class="checkbox-big-label">@LANG('ui.Match Whole Word')</label>
            </div>
        </div>
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
                        <td>
                            @if ($record->isBook())
                                <a href="/books/show/{{$record->permalink}}" target="_blank">{{Str::startsWith($record->title, $record->source) ? '' : $record->source . ', '}}{{$record->title}}</a>
                            @else
                                <a href="/articles/view/{{$record->permalink}}" target="_blank">{{$record->title}}</a>
                            @endif
                            @if (isset($record->matches))
                                @foreach($record->matches as $match)
                                    <div>{!! $match !!}</div>
                                @endforeach
                            @endif
                        </td>

                    </tr>
                @endforeach
            @endif

            @if (isset($snippets))
                @foreach($snippets as $record)
                    <tr>
                        <td>@LANG('proj.Practice Text')</td>
                        @php
                            $title = str_ireplace($search, highlightText($search), $record->title)
                        @endphp
                        @if (isAdmin() || App\User::isOwner($record->user_id))
                            <td><a href="/definitions/edit/{{$record->id}}" target="_blank">{!! $title !!}</a></td>
                        @else
                            <td><a href="/definitions/view/{{$record->permalink}}" target="_blank">{!! $title !!}</a></td>
                        @endif
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
