@php
	$id = isset($parms['tagId']) ? $parms['tagId'] : 0;
	$count = isset($parms['count']) ? $parms['count'] : 0;
	$order = isset($parms['order']) ? $parms['order'] : '';

    // show the active item
	function c($current, $link) {
	    return ($current == $link) ? 'purple' : '';
	}
@endphp

<div class="middle ml-2">
    <div class="dropdown" >
        <a class="" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"	href="" onclick="">
            <div class="glyphCustom-md glyphicon glyphicon-sort"></div>
        </a>
        <ul class="small-thin-text dropdown-menu dropdown-menu-right" style="z-index:10000; background-color:white;">
            <li><a class="dropdown-item {{c($order, 'desc')}}" href="/definitions/list-tag/{{$id}}?count={{$count}}&order=desc">Newest</a></li>
            <li><a class="dropdown-item {{c($order, 'asc')}}" href="/definitions/list-tag/{{$id}}?count={{$count}}&order=asc">Oldest</a></li>
            <li><a class="dropdown-item {{c($order, 'attempts')}}" href="/definitions/list-tag/{{$id}}?count={{$count}}&order=attempts">Attempts</a></li>
            <li><a class="dropdown-item {{c($order, 'score')}}" href="/definitions/list-tag/{{$id}}?count={{$count}}&order=score">Score</a></li>
            <li><a class="dropdown-item {{c($order, 'views')}}" href="/definitions/list-tag/{{$id}}?count={{$count}}&order=views">Views</a></li>
            <li><a class="dropdown-item {{c($order, 'reads')}}" href="/definitions/list-tag/{{$id}}?count={{$count}}&order=reads">Reads</a></li>
        </ul>
    </div>
</div>
