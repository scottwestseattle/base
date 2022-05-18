@php
    $definitions = isset($results['definitions']) ? $results['definitions'] : null;
    $snippets = isset($results['snippets']) ? $results['snippets'] : null;
    $entries = isset($results['entries']) ? $results['entries'] : null;
    $count = isset($results['count']) ? $results['count'] : null;
    $search = isset($results['search']) ? $results['search'] : null;
    $matches = ($count > 1 || $count == 0) ? 'matches' : 'match';
@endphp
<div class="table" style="" id="searchDefinitionsResultsTable">
    <table  class="table-responsive table-condensed medium-text" style="">
        <thead>
            <tr>
                <td style="xmin-width: 100px;">
                    "{{$search}}": {{$count}} {{$matches}}
                </td>
            </tr>
        </thead>
    </table>
    <table class="table table-striped">
        <tbody>

        @if (isset($definitions))
            @foreach($definitions as $record)
                <tr>
                    <td>@LANG('proj.Dictionary')</td>
                    <td><a href="/definitions/view/{{$record->permalink}}" target="">{{$record->title}}</a></td>
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
                        <td><a href="/definitions/edit/{{$record->id}}" target="">{!! $title !!}</a></td>
                    @else
                        <td><a href="/definitions/view/{{$record->permalink}}" target="">{!! $title !!}</a></td>
                    @endif
                </tr>
            @endforeach
        @endif

        @if (isset($entries))
            @foreach($entries as $record)
                <tr>
                    <td>{{trans_choice('proj.' . $record->getTypeFlagName($record->type_flag), 2)}}</td>
                    <td>
                        @if ($record->isBook())
                            <a href="/books/show/{{$record->permalink}}" target="">{{Str::startsWith($record->title, $record->source) ? '' : $record->source . ', '}}{{$record->title}}</a>
                        @else
                            <a href="/articles/view/{{$record->permalink}}" target="">{{$record->title}}</a>
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

        @if (isset($lessons))
            @foreach($lessons as $record)
                <tr>
                    <td>{{trans_choice('proj.Lesson', 2)}}</td>
                    <td><a href="/lessons/view/{{$record->id}}" target="">{{$record->courseTitle}} - {{$record->lesson_number}}.{{$record->section_number}} {{$record->title}}</a></td>
                </tr>
            @endforeach
        @endif

        @if (isset($words))
            @foreach($words as $record)
                <tr>
                    @if ($record->isLessonWord())
                        <td>@LANG('content.Lesson')</td>
                        <td><a href="/lessons/view/{{$record->parent_id}}" target="">{{$record->courseTitle}} - {{$record->lesson_number}}.{{$record->section_number}} {{$record->lessonTitle}}</a></td>
                    @elseif ($record->isVocabListWord())
                        <td>@LANG('content.Vocabulary')</td>
                        <td>
                            <a href="/words/view/{{$record->id}}" target="_blank">{{$record->title}}</a>
                            &nbsp;(<a href="/vocab-lists/view/{{$record->vocab_list_id}}" target="">@LANG('content.List')</a>)
                        </td>
                    @else
                        <td>@LANG('content.Vocabulary')</td>
                        <td><a href="/words/view/{{$record->id}}" target="">{{$record->title}}</a></td>
                    @endif
                </tr>
            @endforeach
        @endif

        </tbody>
    </table>
</div>
