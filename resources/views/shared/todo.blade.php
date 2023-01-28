@if (isset($options['records']))
@php
    $bgActive = App\DateTimeEx::getDayColor();
    $bgActiveLight = App\DateTimeEx::getDayColorLight($bgActive);
    $bgDone = '#bfbfbf';
    $bgDoneLight = $bgDone;
    $doneCount = $options['doneCount'];
    $count = count($options['records']);
    $allDone = ($count === $doneCount);
@endphp
@if ($allDone)
    <div class="mb-1 small-thin-text"><a type="button" class="btn btn-success btn-xs" style="border-radius: 10px; padding: 5px 8px;"
        href="" onclick="event.preventDefault(); $('#showPracticeList').toggle();">@LANG('proj.Finished Daily Practice - Show')</a>
    </div>
@endif
<div id="showPracticeList" class="mb-3 {{$allDone ? 'hidden' : ''}}">
    <h3 class="mb-0">@LANG('proj.Daily Practice')<span class="title-count">({{$count}})</span></h3>
    <div class="small-thin-text">{{App\DateTimeEx::getShortDateTime(null, 'M d, Y')}} (GMT {{App\DateTimeEx::getTimezoneOffset()}})</div>
    @if ($doneCount > 0 && !$allDone)
        <div class="mb-1 small-thin-text">
            <a type="button" class="btn btn-primary btn-xs" style="border-radius: 10px; padding: 5px 8px;" href=""
            onclick="event.preventDefault(); $('.done').toggle();">@LANG('proj.:count items finished - Show', ['count' => $doneCount])</a>
        </div>
    @endif
    <table style="width:100%;">
        @foreach($options['records'] as $record)
        @php
            $bg = ($record['done']) ? $bgDone : $bgActive;
            $bgLight = $record['done'] ? $bgDoneLight : $bgActiveLight;
            //dump($record);
        @endphp
        <tr class="mb-3 {{($record['done'] && !$allDone) ? 'done hidden' : ''}}" style="border: 0px white solid; color:white; background: linear-gradient(180deg, {{$bgLight}}, {{$bg}});">
            <td class="px-2 py-2">
                <a class="link-bold " style="color:white;" href="{{$record['linkUrl']}}">
                    <svg class="float-left bi mt-1 mr-2" width="16" height="16" fill="currentColor" ><use xlink:href="/img/bootstrap-icons.svg#{{$record['icon']}}" /></svg>
                    <b>{{$record['title']}}</b>
                    <div class="medium-thin-text"><b>{{$record['linkTitle']}}</b></div>
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endif
