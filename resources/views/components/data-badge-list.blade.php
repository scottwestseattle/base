@if (isset($records))
	@if (isset($title))
		<h4>@LANG('content.' . $title) <a href="/words/index" class="btn btn-outline-primary btn-xs" role="button">Show All</a></h4>
	@endif
	<div class="mb-3" style="{{isset($fontStyle) ? $fontStyle : 'font-size:.8em'}};">
		@foreach($records as $record)
			<span class="badge badge-info vocab-pills">
			@if (isset($edit))
				<a href="{{$edit}}{{$record->id}}">{{$record->title}}</a>
			@else
				{{$record->title}}
			@endif
			</span>
		@endforeach
	</div>
@endif