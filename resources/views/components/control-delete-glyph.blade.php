@php
$size = isset($size) ? $size : 16;
@endphp
<div class="dropdown {{isset($margin) ? $margin : ''}}" >
	<a class="" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"	href="" onclick="" tablindex="-1">
        <svg class="mt-1 text-primary" width="{{$size}}" height="{{$size}}" >
            <use xlink:href="/img/bootstrap-icons.svg#{{$svg}}" />
        </svg>
	</a>
	<ul class="small-thin-text dropdown-menu dropdown-menu-right">
		<li><a id="a0" class="dropdown-item" href="{{$href}}" onclick="{{isset($onclick) ? $onclick : ''}}">{{__($prompt)}}</a></li>
	</ul>
</div>
