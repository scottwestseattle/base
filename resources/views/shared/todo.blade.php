@if (isset($options['todo']))
@php
    $bgActive = App\DateTimeEx::getDayColor();
    $bgActiveLight = App\DateTimeEx::getDayColorLight($bgActive);
    $bgDone = '#bfbfbf';
    $bgDoneLight = $bgDone;
    $done = true;
    foreach($options['todo'] as $record)
    {
        if (!$record['done'])
        {
            $done = false;
            break;
        }
    }

    $title = __('proj.Daily Practice');
@endphp
    @if ($done)
        <div class="mb-1 small-thin-text"><a href="" onclick="event.preventDefault(); $('#showPracticeList').toggle();">{{$title}} - Finished - Show</a></div>
    @endif
    <div id="showPracticeList" class="mb-3 {{$done ? 'hidden' : ''}}">
        <h3 class="mb-0">{{$title}}</h3>
        <div class="mb-2 small-thin-text">{{App\DateTimeEx::getShortDateTime(null, 'M d, Y')}} (GMT {{App\DateTimeEx::getTimezoneOffset()}})</div>
        <table style="width:100%;">
            @foreach($options['todo'] as $record)
            @php
                $bg = $record['done'] ? $bgDone : $bgActive;
                $bgLight = $record['done'] ? $bgDoneLight : $bgActiveLight;
            @endphp
            <tr class="mb-3" style="border: 0px white solid; color:white; background: linear-gradient(180deg, {{$bgLight}}, {{$bg}});">
                <td class="px-2 py-2">
                    <div class="">
                        <svg class="float-left bi mt-1 mr-2" width="16" height="16" fill="currentColor" ><use xlink:href="/img/bootstrap-icons.svg#{{$record['icon']}}" /></svg>
                        <b>{{$record['action']}}</b>
                    </div>
                    <div class="medium-thin-text"><a style="color:white;" href="{{$record['linkUrl']}}"><b>{{$record['linkTitle']}}</b></a></div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
@endif
