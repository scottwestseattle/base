@if (isset($options['todo']))
@php
    $bg = App\DateTimeEx::getDayColor();
    $bgLight = App\DateTimeEx::getDayColorLight($bg);
@endphp
<div class="mb-3">
    <div class="large-thin-text mb-2">@LANG('proj.Daily Practice'): {{App\DateTimeEx::getShortDateTime(null, 'M d')}}</div>
    <table style="width:100%;">
        @foreach($options['todo'] as $record)
        <tr class="mb-3" style="border: 0px white solid; color:white; background: linear-gradient(180deg, {{$bgLight}}, {{$bg}});">
            <td class="px-3 py-2">
                <div class=""><b>{{$record['action']}}</b></div>
                <div class="medium-thin-text"><a style="color:white;" href="{{$record['linkUrl']}}"><b>{{$record['linkTitle']}}</b></a></div>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endif
