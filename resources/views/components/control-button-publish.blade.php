@if (isAdmin())

    @php
        $locale = App()->getLocale();
		$showPublic = isset($showPublic) ? $showPublic : false;
		$published = App\Status::getReleaseStatus($record->release_flag);
		$finished = App\Status::getWipStatus($record->wip_flag);
		$btnStyle =  isset($btnStyle) ? $btnStyle : 'btn-xs';
		$ajax =  isset($ajax) ? $ajax : false;
		$reload =  isset($reload) && $reload ? '' : 'event.preventDefault;';
		$publishUpdateUrl = route('articles.publishUpdateGet', ['locale' => $locale, 'entry' => $record->id]);
		$publishUrl = route('articles.publish', ['locale' => $locale, 'entry' => $record->id]);
    @endphp

	@if ($showPublic || !$published['done'])
	    @if ($ajax)
    		<a class="btn {{$published['class']}} {{$btnStyle}}" role="button" href="" onclick="{{$reload}} ajaxexec('{{$publishUpdateUrl}}')">{{__($published['label'])}}</a>
    	@else
    		<a class="btn {{$published['class']}} {{$btnStyle}}" role="button" href="{{$publishUrl}}">{{__($published['label'])}}</a>
		@endif
	@endif

	@if (false && !$finished['done'])
		<a class="btn {{$finished['class']}} {{$btnStyle}}" role="button" href="/{{$prefix}}/publish/{{$record->id}}">{{__($finished['label'])}}</a>
	@endif

@endif
