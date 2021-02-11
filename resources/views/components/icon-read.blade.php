@php
    $float = isset($float) ? $float : 'float-left';
    $color = isset($color) ? $color : '';
@endphp

@if (!isset($nodiv) || !$nodiv)
<div class="mr-2" style="display: {{$float}};">
@endif

@if (isset($href))
	<a href='{{$href}}' style="text-decoration:none;">
@else
	<a href="" onclick='{{$onclick}}'  style="text-decoration:none;">
@endif
		<span style="font-size:20px;" class="glyphCustom glyphicon glyphicon-volume-up {{$color}}"></span>
	</a>

@if (!isset($nodiv) || !$nodiv)
</div>
@endif
