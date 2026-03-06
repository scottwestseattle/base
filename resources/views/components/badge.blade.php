@php
$class = isset($class) ? $class : 'badge-dark badge-purple badge-medium';
$style = isset($style) ? $style : '';
@endphp
@if (!empty($text))
<div class="badge {{$class}}" style="{{$style}}">{{$text}}</div>
@endif
