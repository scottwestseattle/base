@php
	$lists = isset($lists) ? $lists : [];
	$tagCount = count($record->tags);
	$tagFromId = ($tagCount > 0) ? $record->tags->first()->id : 0;
	$id = isset($id) ? $id : 1;
	$heart = ($tagCount > 0) ? 'heart' : 'heart-empty';
	$heartId = 'heart' . $id . '-' . $record->id . '';
    $status = isset($status) ? $status : ('status' . $id . '-' . $record->id . '');
@endphp

<div class="middle ml-2">

@if (count($lists) <= 1)

    <a href='' onclick="heartDefinition(event, {{$record->id}}, '#{{$status}}')">
        <span id="{{$heartId}}" class="glyphCustom-md glyphicon glyphicon-{{$heart}}"></span>
    </a>

@else

    <div class="dropdown" >
        <a class="" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"	href="" onclick="">
            <div class="glyphCustom-md glyphicon glyphicon-{{$heart}}"></div>
        </a>

        <ul class="small-thin-text dropdown-menu dropdown-menu-right">
            @foreach($lists as $list)
                @if ($tagFromId == $list->id)
                    <li><a class="dropdown-item steelblue" href="/definitions/set-favorite-list/{{$record->id}}/{{$tagFromId}}/0">Remove from {{$list->name}}</a></li>
                @else
                    <li><a class="dropdown-item" href="/definitions/set-favorite-list/{{$record->id}}/{{$tagFromId}}/{{$list->id}}">{{$list->name}}</a></li>
                @endif
            @endforeach
        </ul>
    </div>

@endif

</div>
