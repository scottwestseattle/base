@if (isset($options['todo']))
@php
    $bg = App\DateTimeEx::getDayColor();
    $bgLight = App\DateTimeEx::getDayColorLight($bg);
@endphp
<div class="mb-3">
    <h3 class="mb-0">@LANG('proj.Daily Practice')</h3>
    <div class="mb-2 small-thin-text">{{App\DateTimeEx::getShortDateTime(null, 'M d, Y')}} (GMT {{App\DateTimeEx::getTimezoneOffset()}})</div>
    <table style="width:100%;">
        @foreach($options['todo'] as $record)
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
