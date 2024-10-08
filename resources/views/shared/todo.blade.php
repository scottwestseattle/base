@if (isset($options['records']) && count($options['records']) > 0)
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
        href="" onclick="event.preventDefault(); $('#showPracticeList').toggle();">@LANG('proj.Finished Daily Practice - Show') ({{$count}})</a>
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
                <svg class="float-left bi mt-1 mr-2" width="16" height="16" fill="currentColor" ><use xlink:href="/img/bootstrap-icons.svg#{{$record['icon']}}" /></svg>
                <b>{{$record['title']}}: </b>
                <span class="medium-thin-text">
                    @if (isset($record['linkUrl2']))
                        <b>{{$record['linkTitle']}}</b>
                        <a type="button" class="link-bold btn btn-sm btn-success" href="{{$record['linkUrl2']}}"><b>@LANG('ui.Newest')</b></a>
                        <a type="button" class="link-bold btn btn-sm btn-warning" href="{{$record['linkUrl']}}"><b>@LANG('proj.Least Used')</b></a>
                        @if (isset($record['linkUrl3']))
                            <a type="button" class="link-bold btn btn-sm btn-danger" href="{{$record['linkUrl3']}}"><b>@LANG('proj.Lowest Score')</b></a>
                        @endif
                    @else
                        <a class="link-bold " style="color:white;" href="{{$record['linkUrl']}}"><b>{{$record['linkTitle']}}</b></a>
                    @endif
                </span>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endif
