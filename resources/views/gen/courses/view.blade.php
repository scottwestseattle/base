@php
    $prefix = 'courses';
@endphp
@extends('layouts.app')
@section('title', trans_choice('proj.Course', 2) )
@section('menu-submenu')@component('gen.courses.menu-submenu', ['prefix' => $prefix, 'record' => $record])@endcomponent @endsection
@section('content')

	<div class="page-nav-buttons">
		<a class="btn btn-success btn-md" role="button" href="/{{$prefix}}/">@LANG('content.Back to Course List')
		<span class="glyphicon glyphicon-button-back-to"></span>
		</a>
	</div>

	<h3 name="title" class="">{{$record->title}}
		@if (isAdmin())
			@if (!\App\Status::isFinished($record->wip_flag))
				<a class="btn {{($wip=\App\Status::getWipStatus($record->wip_flag))['btn']}} btn-xs" role="button" href="/{{$prefix}}/publish/{{$record->id}}">{{$wip['text']}}</a>
			@endif
			@if (!\App\Status::isPublished($record->release_flag))
				<a class="btn {{($release=\App\Status::getReleaseStatus($record->release_flag))['btn']}} btn-xs" role="button" href="/{{$prefix}}/publish/{{$record->id}}">{{$release['text']}}</a>
			@endif
		@endif
	</h3>

	<p>{{$record->description}}</p>

    <a href="/lessons/view/{{$firstId}}">
        <button type="button" style="text-align:center; font-size: 1.3em; color:white;" class="btn btn-info btn-lesson-index" {{$disabled}}>@LANG('content.Start at the beginning')</button>
    </a>

	<h1>@LANG('content.Lessons') ({{$displayCount}})
	@if (isAdmin())
		<span><a href="/lessons/admin/{{$record->id}}"><span class="glyphCustom glyphicon glyphicon-admin"></span></a></span>
		<span><a href="/lessons/add/{{$record->id}}"><span class="glyphCustom glyphicon glyphicon-add"></span></a></span>
	@endif
	</h1>

<div class="accordion" id="accordionExample">
@foreach($records as $record)

@if (count($records) == 1)

@foreach($record as $rec)
	<a href="/lessons/view/{{$rec->id}}">
	<button style="" type="button" class="btn btn-outline-info btn-lesson-index">
		<table>
			<tr>
				<td>
					<div style="font-size:1em; color:purple; padding-right:5px;">
						{{$rec->section_number}}.&nbsp;{{$rec->title}}
					</div>
					<span style="font-size:12px; color:#D64D32;">{{$rec->description}}</span>

					@if (isAdmin())
						<?php $published = $rec->getStatus(); $finished = $rec->getFinishedStatus(); ?>
						@if (!$published['done'] || !$finished['done'])
						<div>
							@if (!$published['done'])
								<a class="btn {{$published['btn']}} btn-xs" role="button" href="/lessons/publish/{{$rec->id}}">{{$published['text']}}</a>
							@endif
							@if (!$finished['done'])
								<a class="btn {{$finished['btn']}} btn-xs" role="button" href="/lessons/publish/{{$rec->id}}">{{$finished['text']}}</a>
							@endif
						</div>
						@endif
					@endif
				</td>
			</tr>
		</table>
	</button>
	</a>
@endforeach

@else

  <div class="card">
    <div style=""  class="card-header" id="headingOne">
      <h3 style=""  class="mb-0">
        <button style="text-decoration:none; text-align:left;" class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{$record[0]->id}}" aria-expanded="true" aria-controls="collapseOne">
			@LANG('content.Lesson')&nbsp;{{$record[0]->lesson_number}}:&nbsp;{{isset($record[0]->title_chapter) ? $record[0]->title_chapter : $record[0]->title}}
		</button>
      </h3>
    </div>

	<div id="collapse{{$record[0]->id}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
		<div style="" class="card-body">
		@foreach($record as $rec)
			<a href="/lessons/view/{{$rec->id}}">
			<button style="" type="button" class="btn btn-outline-info btn-lesson-index link-dark">
				<table>
					<tr>
						<td>
							<div style="font-size:1em; color:purple; padding-right:5px;">{{$rec->lesson_number}}.{{$rec->section_number}}&nbsp;{{$rec->title}}</div>
							<span style="font-size:12px; color:#D64D32;">{{$rec->description}}</span>
						</td>
					</tr>
				</table>
			</button>
			</a>
		@endforeach
		</div>
    </div>
  </div>

@endif

@endforeach

</div>

@if (false)
	<a href="/lessons/view/{{$record[0]->id}}">
		<button style="" type="button" class="btn btn-outline-info btn-lesson-index link-dark">
			<table>
				<tr>
					<td>
						<div style="font-size:1.3em; color:purple; padding-right:5px;">Chapter {{$record[0]->lesson_number}}:&nbsp;{{$record[0]->title}}</div>
						<span style="font-size:.9em">{{$record[0]->description}}</span>
					</td>
				</tr>
			</table>
		</button>
	</a>
@endif

@endsection
