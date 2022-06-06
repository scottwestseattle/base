@php
    $prefix = 'lessons';
@endphp
@extends('layouts.app')
@section('title', trans_choice('proj.Course', 2) )
@section('menu-submenu')@component('gen.' . $prefix . '.menu-submenu', ['prefix' => $prefix, 'record' => $record])@endcomponent @endsection
@section('content')
	<div class="page-nav-buttons">
		<a class="btn btn-success btn-sm btn-nav-lesson-sm" role="button"
            @if($record->isText())
                href="/courses/view/{{$record->parent_id}}"
            @else
                href="/lessons/start/{{$record->id}}"
		    @endif
		>@LANG('proj.Back to')&nbsp;{{$courseTitle}}<span class="glyphicon glyphicon-circle-arrow-up"></span></a>
		<a class="btn btn-success btn-sm btn-nav-lesson-sm {{isset($nextChapter) ? '' : 'hidden'}}" role="button" href="/lessons/view/{{$nextChapter}}">@LANG('proj.Next Chapter')<span class="glyphicon glyphicon-circle-arrow-right"></span></a>
	</div>

	<div class="page-nav-buttons">
		<a class="btn btn-primary btn-lg btn-nav-lesson {{isset($prev) ? '' : 'disabled'}}" role="button" href="/{{$prefix}}/view/{{$prev}}"><span class="glyphicon glyphicon-circle-arrow-left"></span>@LANG('ui.Prev')</a>
		<a class="btn btn-primary btn-lg btn-nav-lesson {{isset($next) ? '' : 'disabled'}}" role="button" href="/{{$prefix}}/view/{{$next}}">@LANG('ui.Next')<span class="glyphicon glyphicon-circle-arrow-right"></span>
		</a>
	</div>

    <div style="font-size:.8em;">
		@if ($record->isTimedSlides())
			{{$courseTitle}}&nbsp;Slide&nbsp;{{$record->section_number}}, Course Time: {{$times['timeTotal']}}
		@else
			{{$courseTitle}},&nbsp;{{trans_choice('proj.Chapter', 1)}}&nbsp;{{$record->lesson_number}}.{{$record->section_number}}&nbsp;({{$sentenceCount}})
		@endif

		@if (isAdmin())
			@if ($record->isVocab())
				&nbsp;<a href="/words/add/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-pencil"></span></a>
			@else
				&nbsp;<a href="/{{$prefix}}/edit2/{{$record->id}}"><span class="glyphCustom-sm glyphicon glyphicon-pencil"></span></a>
			@endif
			@if (!($published=$record->getStatus())['done'])
				<a class="btn {{$published['btn']}} btn-xs" role="button" href="/{{$prefix}}/publish/{{$record->id}}">{{$published['text']}}</a>
			@endif
			@if (!($finished=$record->getFinishedStatus())['done'])
				<a class="btn {{$finished['btn']}} btn-xs" role="button" href="/{{$prefix}}/publish/{{$record->id}}">{{$finished['text']}}</a>
			@endif
		@endif
	</div>

	<h3 name="title" class="mb-2">

		@if ($record->isReading())
			@component('components.icon-read', ['href' => "/lessons/read/$record->id"])@endcomponent
		@endif

	    {{$record->title }}
	    @if (false && $record->isText())
            <div><a href="/lessons/convert-to-list/{{$record->id}}"><button class="btn btn-info btn-xs">Convert</button></a></div>
        @endif
    </h3>

    @if ($record->isTimedSlides() && isset($record->main_photo))
        <div>
            {{$record->getTime()['runSeconds']}} seconds
            &nbsp;({{$record->getTime()['breakSeconds']}} break)
        </div>
        <div><img src="{{$photoPath}}{{$record->main_photo}}" style="width: 100%; max-width: 400px;"/></div>
    @endif

	@if (strlen($record->description) > 0)
		<p class="">{{$record->description }}</p>
	@endif

	@if ($record->isQuiz())

		@if (isAdmin())

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            @if ($record->isTranslation())
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><span class="nav-link-tab">{{trans_choice('ui.Text', 1)}}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><span class="nav-link-tab">{{trans_choice('ui.Translation', 1)}}</span>({{$sentenceCount}})</a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><span class="nav-link-tab">{{trans_choice('proj.Exercise', 1)}}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><span class="nav-link-tab">{{trans_choice('proj.Question', 2)}}</span>({{$sentenceCount}})</a>
                </li>
            @endif
        </ul>

		@else

        @if ($record->isTranslation())
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><span class="nav-link-tab">{{trans_choice('ui.Text', 1)}}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><span class="nav-link-tab">{{trans_choice('ui.Translation', 1)}}</span>({{$sentenceCount}})</a>
                </li>
            </ul>
        @endif

		@endif

		<div class="tab-content" id="myTabContent">

			<!------------------------------------------------------------------------------->
			<!-- The quiz launch tab                                                       -->
			<!------------------------------------------------------------------------------->

			<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
				<div style="min-height:300px;">

                @if ($record->isMc())
					<div style="margin: 10px 0;">
						<a href="/lessons/review/{{$record->id}}/2"><button class="btn btn-success">Review ({{$sentenceCount}})</button></a>
						@if ($sentenceCount > 20)
						    <a href="/lessons/review/{{$record->id}}/2/20"><button class="btn btn-success">Review (20)</button></a>
						@endif
					</div>
                @elseif ($record->isFlashcards())
					<div style="margin: 10px 0;">
						<a href="/lessons/review/{{$record->id}}/1"><button class="btn btn-success">Flashcards ({{$sentenceCount}})</button></a>
						@if ($sentenceCount > 20)
    						<a href="/lessons/review/{{$record->id}}/1/20"><button class="btn btn-success">Flashcards (20)</button></a>
                        @endif
					</div>
                @elseif ($record->isTranslation())
					<div style="margin: 10px 0;">
						<a href="/lessons/review/{{$record->id}}/1"><button class="btn btn-success">Flashcards ({{$sentenceCount}})</button></a>
					</div>
                @endif

                @if (false)
                    @if ($record->getLessonType() == LESSON_TYPE_QUIZ_MC1)
                    <div style="margin: 20px 0;">
                        <a href="/lessons/reviewmc/{{$record->id}}/{{LESSON_TYPE_QUIZ_MC1}}"><button class="btn btn-primary">Start Quiz</button></a>
                    </div>
                    @elseif ($record->getLessonType() == LESSON_TYPE_QUIZ_MC2)
                    <div style="margin: 20px 0;">
                        <a href="/lessons/review/{{$record->id}}/{{LESSON_TYPE_QUIZ_MC2}}"><button class="btn btn-info">Start Quiz</button></a>
                    </div>
                    @elseif ($record->getLessonType() == LESSON_TYPE_QUIZ_MC3)
                    <div style="margin: 20px 0;">
                        <a href="/lessons/reviewmc/{{$record->id}}/{{LESSON_TYPE_QUIZ_MC3}}"><button class="btn btn-info">Start Quiz</button></a>
                    </div>
                    @elseif ($record->getLessonType() == LESSON_TYPE_QUIZ_MC4)
                    <div style="margin: 20px 0;">
                        <a href="/lessons/reviewmc/{{$record->id}}/{{LESSON_TYPE_QUIZ_MC4}}"><button class="btn btn-info">Start Quiz</button></a>
                    </div>
                    @elseif ($record->getLessonType() == LESSON_TYPE_QUIZ_MC4)
                    <div style="margin: 20px 0;">
                        <a href="/lessons/reviewmc/{{$record->id}}/{{LESSON_TYPE_QUIZ_MC}}"><button class="btn btn-success">Start Review</button></a>
                    </div>
                    @endif
                @endif

            	<div>{!! $record->text !!}</div>

				</div>
			</div>

			<!------------------------------------------------------------------------------->
			<!-- The quiz launch tab raw view                                              -->
			<!------------------------------------------------------------------------------->
			<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
			    @if ($record->isTranslation())
    				<div class="mt-2">{!! $record->text_translation !!}</div>
				@else
    				<p>{!! $record->text !!}</p>
				@endif
			</div>

		</div>

	@elseif ($record->isVocab())

		<!------------------------------------------------------------------------------->
		<!-- The vocab view                                              -->
		<!------------------------------------------------------------------------------->

		<!-- the default text at the top of the vocab list -->
		@if (strlen($record->text) > 0)
			<p>{!! $record->text !!}</p>
		@elseif (strlen($record->description) > 0)
			<p>{!! $record->description !!}</p>
		@else
			<p>To begin, please look up the following words in your native language and add them to your vocabulary list.</p>
			@if (!Auth::check())
				<p>An account is required to save vocabulary definitions.</p>
				<p>If you already have an account, please <a href="/login">click here to login</a>, otherwise please <a href="/register">click here to register a new account</a>.</p>
			@endif
		@endif

		@if (isset($vocab) && count($vocab) > 0)
		<div class="xrow">

			<!-- repeat this block for each column -->
			<div class="xcol-sm"><!-- need to split word list into multiple columns here -->

				<ul class="nav nav-tabs" id="vocabTab" role="tablist">
					<li class="nav-item">
						<a class="nav-link {{$hasDefinitions ? 'active' : ''}}" id="view-tab" data-toggle="tab" href="#view" role="tab" aria-controls="view" aria-selected="true">@LANG('View')&nbsp;({{count($vocab)}})</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{!$hasDefinitions ? 'active' : ''}}" id="edit-tab" data-toggle="tab" href="#edit" role="tab" aria-controls="edit" aria-selected="false">@LANG('Edit')&nbsp;({{count($vocab)}})</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="/lessons/view/{{$record->id}}" ><span style="font-size:.7em;" class="glyphicon glyphicon-refresh"></span></a>
					</li>
				</ul>

				<div class="tab-content" id="vocabTabContent">

				<div class="tab-pane fade {{$hasDefinitions ? 'show active' : ''}}" id="view" role="tabpanel" aria-labelledby="view-tab">
					<div class="table">
						<table class="table-responsive table-borderless">
							<tbody>
								@foreach($vocab as $word)
								<tr>
									<td>{{$word->title}}</td>
									<td>{{$word->description}}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				<div class="tab-pane fade {{!$hasDefinitions ? 'show active' : ''}}" id="edit" role="tabpanel" aria-labelledby="edit-tab">
					<div class="">
						<table class="table table-borderless">
							<tbody>
								@foreach($vocab as $word)
								<tr>
									<td>
										<div class="vocab-title">{{$word->title}}</div>
										<div class="vocab-accent-popup" id="float{{$word->id}}"><!-- this is where the accent pop-up will be shown --></div>
										<div class="vocab-description">
											<form id="form{{$word->id}}" method="POST" action="/lessons/view/{{$record->id}}">
												<input type="hidden" name="type_flag" value="{{WORDTYPE_LESSONLIST_USERCOPY}}" />
												<input name="description" id="text{{$word->id}}" class="form-control" type="text"
													onfocus="setFloat($(this), 'float{{$word->id}}');"
													onblur="ajaxPost('/words/updateajax/{{$word->id}}', 'form{{$word->id}}', 'result{{$word->id}}');"
													value="{{$word->description}}"
												/>
												<div class="vocab-save-results" id="result{{$word->id}}"></div>
												{{csrf_field()}}
											</form>
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				</div>

			</div>
			<!-- end of repeat block -->

			@component('components.control-accent-chars-esp')@endcomponent

		</div>
		@endif

	@else

		<!------------------------------------------------------------------------------->
		<!-- The lesson text view -->
		<!------------------------------------------------------------------------------->
		<p>{!! $record->text !!}</p>

        @if (false && $record->isText())
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input id="checkboxFinished" type="checkbox" onClick="setFinished()" aria-label="Checkbox for following text input">
                </div>
              </div>
                <a class="btn btn-info btn-sm" role="button" onClick="setFinished()" href="#">@LANG('lesson.Finished')</a>
            </div>
        @endif

	@endif

    @if ($record->isText())
	<div class="page-nav-buttons">
		<a class="btn btn-primary btn-lg btn-nav-lesson {{isset($prev) ? '' : 'disabled'}}" role="button" href="/{{$prefix}}/view/{{$prev}}"><span class="glyphicon glyphicon-circle-arrow-left"></span>@LANG('ui.Prev')</a>
		<a class="btn btn-primary btn-lg btn-nav-lesson {{isset($next) ? '' : 'disabled'}}" role="button" href="/{{$prefix}}/view/{{$next}}">@LANG('ui.Next')<span class="glyphicon glyphicon-circle-arrow-right"></span></a>
	</div>
	@endif

	@component('gen.lessons.comp-lesson-list', ['records' => $lessons, 'tableClass' => 'table-lesson-list', 'selectedId' => $record->id])@endcomponent

</div>
@endsection

<script>

function setFinished()
{
	event.preventDefault();
	var check = !$('#checkboxFinished').prop('checked');

	$('#checkboxFinished').prop('checked', check);
}

</script>
