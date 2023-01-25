@php
    $bgLast = -1;
    $records = isset($history['records']) ? $history['records'] : [];
    $counts = isset($history['counts']) ? $history['counts'] : [];
    $countDays = 0;
    $maxDays = isset($history['maxDays']) ? $history['maxDays'] : PHP_INT_MAX;
@endphp
<div>
    <h1 class="mb-0">{{trans_choice('ui.History', 2)}}</h1>
    <div class="">
    <table class="">
    @foreach ($records as $record)
        @php
            $bg = App\DateTimeEx::getDayColor($record->created_at);
            $bgLight = App\DateTimeEx::getDayColorLight($bg);

            // color tester: $bg = App\DateTimeEx::getColor(100 + $loop->iteration);
            $info = $record->getInfo();
        @endphp
        @if ($bg !== $bgLast)
            @php
                $countDays++;
                if ($countDays > $maxDays)
                    break;

                $bgLast = $bg;
                $day = App\DateTimeEx::getShortDateTime($record->created_at, 'm-d-Y');
                $dayShow = App\DateTimeEx::isToday($record->created_at) ? __('dt.Today') : App\DateTimeEx::getShortDateTime($record->created_at, 'l, M d');
                $count = isset($counts[$day]) ? $counts[$day] : 0;
            @endphp
            <tr><td><span class="large-thin-text" style="line-height:50px;">{{$dayShow}} ({{$count}})</span></td></tr>
        @endif
        <tr class="mb-3" style="border: 0px white solid; color:white; background: linear-gradient(180deg, {{$bgLight}}, {{$bg}});">
            <td class="p-3">
                <div class="text-center">
                    <div class="small-thin-text">{{App\DateTimeEx::getShortDateTime($record->created_at, 'M d, Y')}}</div>
                    @if ($info['hasUrl'])
                        <div><a class="white" href="{{$info['url']}}">{{$info['actionName']}}: {{$info['programName']}}</a> ({{$info['stats']}})</div>
                    @else
                        <div>{{$info['actionName']}}: {{$info['programName']}} ({{$info['stats']}})</div>
                    @endif
                    @if (true)
                    <div class="small-thin-text">
                        <div>Id: {{$record->program_id}}, Count: {{$record->count}}, Flags: {{$record->type_flag}}/{{$record->subtype_flag}}/{{$record->action_flag}} </div>
                        @if (isset($record->route) && !empty($record->route))
                            <div>Route: {{$record->route}}</div>
                        @endif
                        @if (isset($record->session_name))
                            <div>{{$record->session_name}} ({{$record->session_id}})</div>
                        @endif
                    </div>
                    @endif
                    @if (isAdmin())
                        <div>
                            <a class="white medium-thin-text" href="/history/edit/{{$record->id}}">Edit</a>
                            <a class="white medium-thin-text ml-3" href="/history/confirmdelete/{{$record->id}}">Delete</a>
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    </table>
    <p class="mt-2"><a href="/history">{{__('ui.Show All')}}</a></p>
    </div>
</div>
