@if (isAdmin())

    @php
		$showPublic = isset($showPublic) ? $showPublic : false;
		$published = App\Status::getReleaseStatus($record->release_flag);
		$finished = App\Status::getWipStatus($record->wip_flag);
		$btnStyle =  isset($btnStyle) ? $btnStyle : 'btn-xs';
		$ajax =  isset($ajax) ? $ajax : false;
		$reload =  isset($reload) && $reload ? '' : 'event.preventDefault;';
    @endphp

	@if ($showPublic || !$published['done'])
	    @if ($ajax)
    		<a class="btn {{$published['class']}} {{$btnStyle}}" role="button" href="" onclick="{{$reload}} ajaxexec('/{{$prefix}}/publishupdate/{{$record->id}}')">{{__($published['label'])}}</a>
    	@else
    		<a class="btn {{$published['class']}} {{$btnStyle}}" role="button" href="/{{$prefix}}/publish/{{$record->id}}">{{__($published['label'])}}</a>
		@endif
	@endif

	@if (false && !$finished['done'])
		<a class="btn {{$finished['class']}} {{$btnStyle}}" role="button" href="/{{$prefix}}/publish/{{$record->id}}">{{__($finished['label'])}}</a>
	@endif

@endif
