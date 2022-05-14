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
            // color tester: $bg = App\DateTimeEx::getColor(100 + $loop->iteration);
        @endphp
        @if ($bg !== $bgLast)
            @php
                $countDays++;
                if ($countDays > $maxDays)
                    break;

                $bgLast = $bg;
                $day = App\DateTimeEx::getShortDateTime($record->created_at, 'm-d-Y');
                $dayShow = App\DateTimeEx::getShortDateTime($record->created_at, 'l, M d');
                $count = isset($counts[$day]) ? $counts[$day] : 0;
            @endphp
            <tr><td><span class="large-thin-text" style="line-height:50px;">{{$dayShow}} ({{$count}})</span></td></tr>
        @endif
        <tr class="mb-3" style="border: 1px white solid; background-color:{{$bg}}; color:white;">
            <td class="p-3">
                <div class="text-center">
                    <div class="small-thin-text">{{App\DateTimeEx::getShortDateTime($record->created_at, 'M d, Y')}}</div>
                    <div>{{$record->program_name}}: {{$record->session_name}} ({{$record->seconds}})</div>
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
