@php
    $float = isset($float) ? $float : 'float-left';
    $color = isset($color) ? $color : '';
    $size = isset($size) ? $size : '20';
@endphp

@if (!isset($nodiv) || !$nodiv)
<div class="mr-2" style="display: {{$float}};">
@endif

@if (isset($href))
	<a href='{{$href}}' style="text-decoration:none;">
@else
	<a href="" onclick='{{$onclick}}'  style="text-decoration:none;">
@endif
		<span style="font-size:{{$size}}px;" class="glyphicon glyphicon-volume-up {{$color}}"></span>
	</a>

@if (!isset($nodiv) || !$nodiv)
</div>
@endif
