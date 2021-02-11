@php
    $float = isset($float) ? $float : 'float-left';
    $color = isset($color) ? $color : '';
@endphp

@if (!isset($nodiv) || !$nodiv)
<div class="mr-2" style="display: {{$float}};">
@endif

@if (isset($href))
	<a href='{{$href}}'>
@else
	<a href="" onclick='{{$onclick}}'>
@endif
		<span style="font-size:20px;" class="glyphCustom glyphicon glyphicon-volume-up {{$color}}"></span>
	</a>

@if (!isset($nodiv) || !$nodiv)
</div>
@endif
