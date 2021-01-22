@php
    $r = getReleaseStatus($record->release_flag);
    $btnClass = isset($btnClass) ? $btnClass : 'btn-xs';
@endphp
<a class="btn {{$btnClass}} {{$r['class']}}" type="button" href='/{{$views}}/publish/{{$record->id}}'>{{__($r['label'])}}</a>
