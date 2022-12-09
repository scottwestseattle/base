@php
$class = isset($class) ? $class : 'badge-dark badge-purple badge-medium';
@endphp
@if (!empty($text))
<div class="badge {{$class}}">{{$text}}</div>
@endif
