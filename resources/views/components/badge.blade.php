@php
$class = isset($class) ? $class : 'badge-dark badge-purple badge-medium';
@endphp
<div class="badge {{$class}}">{{$text}}</div>
