@php
$size = isset($size) ? $size : 16;
$isSvg = isset($svg);
$linkText = isset($linkText) ? $linkText : null;
$style = isset($style) ? $style : '';
$margin = isset($margin) ? $margin : 'mb-1';
$id = isset($id) ? $id : 'a0';
@endphp
<div class="dropdown {{$margin}}" >
	<a data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"	href="" onclick="" tablindex="-1">
	    @if ($isSvg)
            <svg class="mt-1 text-primary" style="$style" width="{{$size}}" height="{{$size}}" fill="currentColor" >
                <use xlink:href="/img/bootstrap-icons.svg#trash" />
            </svg>
        @else
            <span>{{__($linkText)}}</span>
        @endif
	</a>
	<ul class="small-thin-text dropdown-menu dropdown-menu-right">
		<li><a id="{{$id}}" class="dropdown-item" href="{{$href}}" onclick="{{isset($onclick) ? $onclick : ''}}">{{__($prompt)}}</a></li>
	</ul>
</div>
