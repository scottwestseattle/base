<?php 
	$title = isset($title) ? $title : 'Vocabulary'; 
	$edit = '/words/edit/';
?>

@if (isset($words))
<h4>@LANG('content.' . $title) ({{count($words)}})</h4>
<div class="mb-3">
	@foreach($words as $lesson)
		<div><a href="/lessons/view/{{$lesson[0]->lessonId}}">{{$lesson[0]->lesson_number}}.{{$lesson[0]->section_number}} - {{$lesson[0]->lessonTitle}} ({{count($lesson)}})</a></div>
		@foreach($lesson as $record)				
			<span class="badge badge-info vocab-pills">
			@if (isset($edit))
				<a href="{{$edit}}{{$record->id}}">{{$record->title}}</a>
			@else
				{{$record->title}}
			@endif
			</span>
		@endforeach
	@endforeach
</div>
@endif	
