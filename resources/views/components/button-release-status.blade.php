@php
    $r = getReleaseStatus($record->release_flag);
@endphp
<a class="btn btn-sm {{$r['class']}}" type="button" href='/{{$views}}/publish/{{$record->id}}'>{{__($r['label'])}}</a>
