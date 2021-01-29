@php
$size = isset($size) ? $size : 16;
$color = isset($color) ? $color : 'text-primary';
@endphp
<svg class="mt-1 {{$color}}" width="{{$size}}" height="{{$size}}" >
    <use xlink:href="/img/bootstrap-icons.svg#{{$svg}}" />
</svg>
