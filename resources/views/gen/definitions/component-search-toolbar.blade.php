<?php
	$tagCount = count($record->tags);
	$heart = ($tagCount > 0) ? 'heart' : 'heart-empty';
	$tagFromId = ($tagCount > 0) ? $record->tags->first()->id : 0;
	$finished = $record->isFinished() ? 'ok-circle' : 'remove-sign';
	$status = 'status' . $id . '-' . $record->id . '';
	$heartId = 'heart' . $id . '-' . $record->id . '';
	$wipId = 'wip' . $id . '-' . $record->id . '';
	$lists = isset($lists) ? $lists : [];
?>

<div class="float-left">
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
	@if (isAdmin())
		<div class="middle ml-2">
			<a href='' onclick="toggleWip(event, {{$record->id}}, '#{{$status}}');">
				<span id="{{$wipId}}" class="glyphCustom-md glyphicon glyphicon-{{$finished}}"></span>
			</a>
		</div>
		<div class="middle ml-2"><a href='/definitions/edit/{{$record->id}}'><span class="glyphCustom-md glyphicon glyphicon-edit"></span></a></div>
		<div class="middle ml-2"><a href='/definitions/confirmdelete/{{$record->id}}'><span class="glyphCustom-md glyphicon glyphicon-delete"></span></a></div>
	@endif
</div>
<div style="clear:both;" id="{{$status}}" class="small-thin-text red"></div>
