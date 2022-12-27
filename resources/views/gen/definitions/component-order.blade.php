@php
	$id = isset($parms['tagId']) ? $parms['tagId'] : 0;
	$count = isset($parms['count']) ? $parms['count'] : 0;
	$order = isset($parms['order']) ? $parms['order'] : '';

    // show the active item
	function c($current, $link) {
	    return ($current == $link) ? 'purple' : '';
	}

	if ($id === 0)
	{
    	$link = '/definitions/favorites-review';
	}
	else
	{
    	$link = '/definitions/list-tag/' . $id;
	}

@endphp

<div class="middle ml-2">
    <div class="dropdown" >
        <a class="" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"	href="" onclick="">
            <div class="glyphCustom-md glyphicon glyphicon-sort"></div>
        </a>
        <ul class="small-thin-text dropdown-menu dropdown-menu-right" style="z-index:{{DEFAULT_BIG_NUMBER}}; background-color:white;">
            <li><a class="dropdown-item {{c($order, 'desc')}}" href="{{$link}}?count={{$count}}&order=desc">Newest</a></li>
            <li><a class="dropdown-item {{c($order, 'asc')}}" href="{{$link}}?count={{$count}}&order=asc">Oldest</a></li>
            <li><a class="dropdown-item {{c($order, 'attempts')}}" href="{{$link}}?count={{$count}}&order=attempts">Attempts - Most</a></li>
            <li><a class="dropdown-item {{c($order, 'attempts-asc')}}" href="{{$link}}?count={{$count}}&order=attempts-asc">Attempts - Fewest</a></li>
            <li><a class="dropdown-item {{c($order, 'score')}}" href="{{$link}}?count={{$count}}&order=score">Score</a></li>
            <li><a class="dropdown-item {{c($order, 'views')}}" href="{{$link}}?count={{$count}}&order=views">Views - Most</a></li>
            <li><a class="dropdown-item {{c($order, 'views-asc')}}" href="{{$link}}?count={{$count}}&order=views-asc">Views - Fewest</a></li>
            <li><a class="dropdown-item {{c($order, 'reads')}}" href="{{$link}}?count={{$count}}&order=reads">Reads - Most</a></li>
            <li><a class="dropdown-item {{c($order, 'reads-asc')}}" href="{{$link}}?count={{$count}}&order=reads-asc">Reads - Fewest</a></li>
        </ul>
    </div>
</div>
