@php
    $history = isset($history) ? $history : [];
@endphp
<div>
    <h1 class="">{{__('History')}}<span class="title-count">({{count($history)}})</span></h1>
    <div class="">
    <table class="">
    @foreach ($history as $record)
        @php
            $bg = App\DateTimeEx::getDayColor($record->created_at);
            // color tester: $bg = App\DateTimeEx::getColor(100 + $loop->iteration);
        @endphp
        <tr class="mb-3" style="border: 1px white solid; background-color:{{$bg}}; color:white;">
            <td class="p-3">
                <div class="text-center">
                    <div class="small-thin-text">{{App\DateTimeEx::getShortDateTime($record->created_at)}}</div>
                    <div>{{$record->program_name}}: {{$record->session_name}} ({{$record->seconds}})</div>
                </div>
            </td>
        </tr>
    @endforeach
    </table>
    <p class="mt-2"><a href="/history">{{__('ui.Show All')}}</a></p>
    </div>
</div>