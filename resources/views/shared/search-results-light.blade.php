@php
    $definitions = isset($results['definitions']) ? $results['definitions'] : null;
    $snippets = isset($results['snippets']) ? $results['snippets'] : null;
    $entries = isset($results['entries']) ? $results['entries'] : null;
    $lessons = isset($results['lessons']) ? $results['lessons'] : null;
    $count = isset($results['count']) ? $results['count'] : null;
    $search = isset($results['search']) ? $results['search'] : null;
    $matches = strtolower(trans_choice('ui.Match', ($count > 1 || $count == 0) ? 2 : 1));
    $locale = app()->getLocale();
@endphp
<div class="table" style="" id="searchDefinitionsResultsTable">
    <table  class="table-responsive table-condensed medium-text">
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
                    <td><a class="m-0" href="{{route('definitions.view', ['locale' => $locale, 'permalink' => $record->permalink])}}" target="">{{$record->title}}</a><div class="small-thin-text">{{$record->translation_en}}</div></td>
                </tr>
            @endforeach
        @endif
        @if (isset($snippets))
            @foreach($snippets as $record)
                <tr>
                    @php
                        $title = str_ireplace($search, highlightText($search), $record->title)
                    @endphp
                    <td>
                        <a class="m-0" href="{{route('definitions.editOrShow', ['locale' => $locale, 'definition' => $record->id])}}" target="">{!! $title !!}</a>
                        <div class="small-thin-text">{{$record->translation_en}}</div>
                    </td>
                </tr>
            @endforeach
        @endif
        @if (isset($entries))
            @foreach($entries as $record)
                <tr>
                    <td>{{trans_choice('proj.' . $record->getTypeFlagName($record->type_flag), 2)}}</td>
                    <td>
                    @if ($record->isBook())
                        <div><a class="m-0" href="{{route('books.show', ['locale' => $locale, 'permalink' => $record->permalink])}}">{{Str::startsWith($record->title, $record->source) ? '' : $record->source . ', '}}{{$record->title}}</a></div>
                    @else
                        <div><a class="m-0" href="{{route('articles.view', ['locale' => $locale, 'permalink' => $record->permalink])}}">{{$record->title}}</a></div>
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
                    <!-- td>{{trans_choice('proj.Lesson', 2)}}</td -->
                    <td><a class="m-0" href="{{route('lessons.view', ['locale' => $locale, 'lesson' => $record->id])}}" target="">{{$record->courseTitle}} - {{$record->lesson_number}}.{{$record->section_number}} {{$record->title}}</a></td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>

