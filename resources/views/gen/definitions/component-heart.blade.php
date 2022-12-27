@php
	$lists = isset($lists) ? $lists : [];
	$tagCount = isset($record->tags) ? count($record->tags) : 0;
	$tagFromId = 0;
	if ($tagCount > 0)
	{
       	$tagFromId = ($tagCount > 0) ? $record->tags->first()->id : 0;
	}
	else
	{
	    $tagCount = isset($record->tag_id) ? 1 : 0;
       	$tagFromId = ($tagCount > 0) ? $record->tag_id : 0;
	}

   	$heart = ($tagCount > 0) ? 'heart' : 'heart-empty';
    $heartId = 'heart' . $id . '-' . $record->id . '';

	$id = isset($id) ? $id : 1;
    $status = isset($status) ? $status : ('status' . $id . '-' . $record->id . '');
@endphp

<div class="middle ml-2">

@if (count($lists) > 1)

    <div class="dropdown" >
        <a class="" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"	href="" onclick="">
            <div class="glyphCustom-md glyphicon glyphicon-{{$heart}}"></div>
        </a>

        <ul class="small-thin-text dropdown-menu dropdown-menu-right" style="z-index:{{DEFAULT_BIG_NUMBER}}; background-color:white;">
            @foreach($lists as $list)
                @if ($tagFromId == $list->id)
                    <li><a class="dropdown-item steelblue" href="/definitions/set-favorite-list/{{$record->id}}/{{$tagFromId}}/0">Remove from {{$list->name}}</a></li>
                @else
                    <li><a class="dropdown-item" href="/definitions/set-favorite-list/{{$record->id}}/{{$tagFromId}}/{{$list->id}}">{{$list->name}}</a></li>
                @endif
            @endforeach
        </ul>
    </div>

@else

    <a href='' onclick="heartDefinition(event, {{$record->id}}, '#{{$status}}')">
        <span id="{{$heartId}}" class="glyphCustom-md glyphicon glyphicon-{{$heart}}"></span>
    </a>

@endif

</div>
