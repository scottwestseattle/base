@if (isAdmin())

	<?php
		$showPublic = isset($showPublic) ? $showPublic : false;
		$published = getReleaseStatus($record->release_flag);
		$finished = getWipStatus($record->wip_flag);
		$btnStyle =  isset($btnStyle) ? $btnStyle : 'btn-xs';
	?>

	@if ($showPublic || !$published['done'])
		<a class="btn {{$published['class']}} {{$btnStyle}}" role="button" href="/{{$prefix}}/publish/{{$record->id}}">{{__($published['label'])}}</a>
	@endif

	@if (!$finished['done'])
		<a class="btn {{$finished['class']}} {{$btnStyle}}" role="button" href="/{{$prefix}}/publish/{{$record->id}}">{{__($finished['label'])}}</a>
	@endif

@endif
