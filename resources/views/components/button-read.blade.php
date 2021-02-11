@php
    $float = isset($float) ? $float : 'float-left';
    $color = isset($color) ? $color : 'btn-primary';
    $class = isset($class) ? $class : '';
@endphp

@if (!isset($nodiv) || !$nodiv)
<div class="mr-2 {{$class}}" style="display: {{$float}};">
@endif

<a href="{{isset($href) ? $href : ''}}" onclick="{{isset($onclick) ? $onclick : ''}}" type="button" class="btn {{$color}}">Open in Reader<span style="font-size:20px;" class="glyphCustom glyphicon glyphicon-volume-up white ml-2"></span></a>

@if (!isset($nodiv) || !$nodiv)
</div>
@endif
