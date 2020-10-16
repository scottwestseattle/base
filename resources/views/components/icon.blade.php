@php
$size = isset($size) ? $size : 16;
@endphp
<svg class="mt-1 text-primary" width="{{$size}}" height="{{$size}}" >
	<use xlink:href="/img/bootstrap-icons.svg#{{$svg}}" />
</svg>
