@extends('layouts.review')
@section('title', __('proj.Flashcards'))
@section('content')
@php
    $quizCount = isset($quizCount) ? $quizCount : $sentenceCount;
    $article = isset($article) ? $article : false;
    $random = isset($random) && $random ? 1 : 0;
    $touchPath = isset($touchPath) ? $touchPath : null;
    $language = getSpeechLanguageShort();
    $programName = isset($history['programName']) ? $history['programName'] : null;

    // set up favorites lists
    // PROBLEMS:
    // todo: need to check for current favorite on each entry because some can be hearted and other's unhearted
    // todo: need to take the unhearted or deleted items out of the rotation during a read or quiz
    $favs = isset($parms['favoriteLists']) ? $parms['favoriteLists'] : [];
    $tagId = isset($parms['tagId']) ? $parms['tagId'] : null;

    // only need reload if not already showing entries OR sort type would change the entries
    $showReload = isset($parms['showReload']) ? $parms['showReload'] : false;
@endphp
<!-------------------------------------------------------->
<!-- Add misc data needed by the JS during runtime -->
<!-------------------------------------------------------->
<div class="data-misc"
	data-max="{{$sentenceCount}}"
	data-quizcount="{{$quizCount}}"
	data-prompt="@LANG('quiz.' . $settings['options']['prompt'])"
	data-prompt-reverse="@LANG('quiz.' . $settings['options']['prompt-reverse'])"
	data-question-count="{{$settings['options']['question-count']}}"
	data-quiztext-round="@LANG('quiz.Round')"
	data-quiztext-correct="@LANG('quiz.Correct')"
	data-quiztext-question="{{trans_choice('quiz.Question', 1)}}"
	data-ismc="{{$isMc}}"
	data-quiztext-of="@LANG('quiz.of')"
	data-quiztext-correct-answer="@LANG('quiz.Correct!')"
	data-quiztext-wrong-answer="@LANG('quiz.Wrong!')"
	data-quiztext-marked-wrong="@LANG('quiz.Answer marked as wrong')"
	data-quiztext-override-correct="@LANG('quiz.Change to Correct')"
	data-quiztext-override-wrong="@LANG('quiz.Change to Wrong')"
	data-quiztext-score-changed="@LANG('quiz.Score Changed')"
	data-random="{{$random}}"
	data-touchpath="{{$touchPath}}"
	data-language="{{$language}}"
	data-locale="{{app()->getLocale()}}"
    @component('components.history-parameters', ['history' => $history])@endcomponent
></div>

	<!-------------------------------------------------------->
	<!-- Add the q and a records -->
	<!-------------------------------------------------------->
@foreach($records as $rec)
	<div class="data-qna"
	    data-question="{{$rec['q']}}"
	    data-answer="{{$rec['a']}}"
	    data-choices="{{isset($rec['choices']) ? $rec['choices'] : ''}}"
	    data-definition="{{$rec['definition']}}"
	    data-extra="{{$rec['extra']}}"
	    data-rule="{{$rec['rule']}}"
	    data-options="{{$rec['options']}}"
	    data-id="{{$rec['id']}}"
	    data-ix="{{$rec['ix']}}" >
	</div>
@endforeach

<div class="container">

	<!-------------------------------------------------------->
	<!-- Header -->
	<!-------------------------------------------------------->
	<div style="margin-top: 5px; margin-bottom:0px;">
		<div id="statsRuntime">
            <!-------------------------------------------------------->
            <!-- Top Return Button -->
            <!-------------------------------------------------------->
			<div class="middle mr-1 mb-0">
                <a href="/"><span style="margin-right:3px;" class="glyphicon glyphicon-home"></span></a>
                <a href="{{$returnPath}}"><button type="button" class="btn btn-xs btn-primary mb-1"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-up"></span>{{trans_choice('ui.Return', 1)}}</button></button></a>
			</div>

            <!-------------------------------------------------------->
            <!-- Run-time Stats -->
            <!-------------------------------------------------------->
			<div class="middle mb-0">
	    		<span id="statsCount" class="mr-2"></span>
    			<span id="statsScore"></span>
		    	<span id="statsAlert"></span><!-- what is this? -->
		    </div>
		</div>
    </div>

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Quiz Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-quiz" class="quiz-panel">

	@if (count($records) > 0)

	<section class="" id='sectionQna'>

	<!-------------------------------------------------------->
	<!-- Instructions -->
	<!-------------------------------------------------------->
	<div class="text-center steelblue" id="" style="font-size: .9em; margin-bottom:10px;">
		<!-------------------------------------------------------->
		<!-- Show the title -->
		<!-------------------------------------------------------->
        <div style="font-weight:500; font-size:1.1em;">{{$programName}}</div>

		<!-------------------------------------------------------->
		<!-- Show Question prompt and results RIGHT/WRONG -->
		<!-------------------------------------------------------->
		<span id="alertPrompt"></span>
	</div>

	<!-------------------------------------------------------->
	<!-- QUESTION -->
	<!-------------------------------------------------------->

	<!-------------------------------------------------------->
	<!-- FLASHCARD -->
	<!-------------------------------------------------------->
	<div id="show-flashcards" class="text-center">
    @if ($article)
        <div class="text-center" style="font-size: {{$settings['options']['font-size']}};">
            <a href="" style="color: black; background-color:LightGray; text-decoration:none;" onclick="flipCard(event, true);">
                <div style="min-height: 300px; ">
                    <div class="" style="">
                        <div id="prompt" class="mb-3"></div>
                    </div>
                    <div class="steelblue">
                        <p id="flashcard-answer" class="hidden"></p>
                        <p id="flashcard-extra" class="large-text hidden"></p>
                    </div>
                </div>
            </a>
        </div>
    @else
        <div class="card card-flashcard card-blue text-center" style="font-size: {{$settings['options']['font-size']}};">
            <a href="" onclick="flipCard(event, true);">
                <div class="card-header">
                    <div class="prompt" class="card-text"></div>
                </div>
                <div class="card-body">
                    <p id="flashcard-answer" class="card-text hidden"></p>
                    <p id="flashcard-extra" class="large-text hidden"></p>
                </div>
            </a>
        </div>
    @endif
	</div>

	<!-------------------------------------------------------->
	<!-- MULTIPLE CHOICE -->
	<!-------------------------------------------------------->
	<div id="show-multiple-choice" class="hidden">
		<fieldset id="runtimeFields">

        <div class="text-center mb-3" style="font-size: {{$settings['options']['font-size']}};">
            <div class="prompt" class=""></div>
        </div>

		<div class="text-center mb-3">
			<!-------------------------------------------------------->
			<!-- SHOW: RIGHT! or WRONG! -->
			<!-------------------------------------------------------->
			<div style="display: none; padding: 10px 0; font-size: {{$settings['options']['font-size']}}; min-height: 70px; margin-top: 20px;" id="answer-show-div"></div>
		</div>

		<!-------------------------------------------------------->
		<!-- ANSWER OPTION BUTTONS  -->
		<!-------------------------------------------------------->
		@php
		    $show = 'none';
		@endphp
		<div style="width:100%;" id="optionButtons" class="">
			<div><button id="0" onclick="checkAnswerFromButtonClick(event)" class="btn btn-primary btn-quiz-mc3" style="display:{{$show}};"></button></div>
			<div><button id="1" onclick="checkAnswerFromButtonClick(event)" class="btn btn-primary btn-quiz-mc3" style="display:{{$show}};"></button></div>
			<div><button id="2" onclick="checkAnswerFromButtonClick(event)" class="btn btn-primary btn-quiz-mc3" style="display:{{$show}};"></button></div>
			<div><button id="3" onclick="checkAnswerFromButtonClick(event)" class="btn btn-primary btn-quiz-mc3" style="display:{{$show}};"></button></div>
			<div><button id="4" onclick="checkAnswerFromButtonClick(event)" class="btn btn-primary btn-quiz-mc3" style="display:{{$show}};"></button></div>
		</div>

		</fieldset>
    </div>

	<div class="">
		<fieldset id="runtimeFields">

	<!----------------------------------------------------------------------------->
	<!-- CONTROL BUTTONS -->
	<!----------------------------------------------------------------------------->

		<!-- BUTTONS ROW 1 -->

		<div class="btn-panel-bottom pb-0 mb-0">
			<button class="btn btn-success btn-quiz" onclick="event.preventDefault(); nextAttempt()" id="button-next-attempt">@LANG('ui.Next')</button>
			<input class="btn btn-default btn-quiz " type="button" value="@LANG('quiz.I KNOW IT') (Alt+k)" onclick="checkAnswer(2)" id="button-know" style="display: default; background-color: green; color: white;">
			<input class="btn btn-default btn-quiz" type="button" value="@LANG('quiz.I DONT KNOW') (Alt+d)" onclick="checkAnswer(3)" id="button-dont-know" style="display: none; background-color: red; color: white;">
			<input class="btn btn-default btn-quiz" type="button" value="@LANG('quiz.Change to Wrong') (Alt+c)" onclick="override()" id="button-override" style="display: none;">
		</div>

		<div class="form-group">
		    <div class="mt-0 pt-0">
			    <button class="btn btn-primary btn-quiz" onclick="event.preventDefault(); checkAnswer(1)" id="button-check-answer">@LANG('quiz.Check Typed Answer')</button>
                @if (false)
                <button class="btn btn-success btn-quiz hidden" onclick="flipCard(event, true)" id="button-remove">@LANG('quiz.Remove')</button>
                @endif

                <div class="text-center">
                    <button class="btn btn-success btn-quiz hidden" onclick="flipCard(event, false)" id="button-repeat">@LANG('quiz.Repeat')</button><!-- this is te I GOT IT WRONG button -->
                </div>
            </div>
			<div class="mt-1 ml-1">
				<input type="checkbox" name="checkbox-flip" id="checkbox-flip" onclick="reloadQuestion('checkbox-flip-flashcards');" />
				<label for="checkbox-flip" class="checkbox-xs steelblue" onclick="reloadQuestion('checkbox-flip-flashcards');">@LANG('quiz.Reverse question and answer')</label>
			</div>
			<div class="mt-1 ml-1">
				<input type="checkbox" name="checkbox-random" id="checkbox-random" onclick="updateRandom();" {{$random ? 'checked' : ''}} />
				<label for="checkbox-random" class="checkbox-xs" onclick="updateRandom();">@LANG('quiz.Random Order')</label>
			</div>

            @php
                $iconSize = 16;
                $iconSizeBigger = $iconSize + 5;
                $iconColor = 'steelblue';
            @endphp
			<div class="small-thin-text">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" id="goToEntry" href="" target="_blank">
                            <svg class="" width="{{$iconSize}}" height="{{$iconSize}}" fill="{{$iconColor}}"><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="readEntry" href="" onclick="event.preventDefault(); read($('#flashcard-answer').html())">
                            <svg class="" width="{{$iconSizeBigger}}" height="{{$iconSizeBigger}}" fill="{{$iconColor}}"><use xlink:href="/img/bootstrap-icons.svg#volume-up" /></svg>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="copyEntry" href="" onclick="clipboardCopy(event, '', 'flashcard-answer', false, true);">
                            <svg class="" width="{{$iconSize}}" height="{{$iconSize}}" fill="{{$iconColor}}"><use xlink:href="/img/bootstrap-icons.svg#clipboard" /></svg>
                        </a>
                        <span class="ml-2" id="copyStatus"></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="" href="" onclick="event.preventDefault(); $('#panel-show').toggle();">
                            <svg class="" width="{{$iconSize}}" height="{{$iconSize}}" fill="{{$iconColor}}"><use xlink:href="/img/bootstrap-icons.svg#list-ol" /></svg>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="dropdown" role="button" href="" onclick="" tablindex="-1">
                            <svg class="" width="{{$iconSize}}" height="{{$iconSize}}" fill="{{$iconColor}}" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
                        </a>
                        <ul class="small-thin-text dropdown-menu dropdown-menu-right">
                            <li><a id="deleteEntryIcon" class="dropdown-item" href="" onclick="">@LANG('ui.Confirm Delete')</a></li>
                        </ul>
                        <span class="ml-2" id="deleteStatus"></span>
	                </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="dropdown" role="button" href="" onclick="" tablindex="-1">
                            <svg class="" width="{{$iconSize}}" height="{{$iconSize}}" fill="{{$iconColor}}" ><use xlink:href="/img/bootstrap-icons.svg#heart{{isset($tagId) ? '-fill' : ''}}" /></svg>
                        </a>
                        <ul id="favs" class="small-thin-text dropdown-menu dropdown-menu-right">
                        @foreach($favs as $list)
                            @if ($tagId == $list->id)
                                <li><a id="unheartEntryIcon" class="dropdown-item steelblue" href="" onclick="">Remove from {{$list->name}}</a></li>
                            @else
                                <li data-tagid="{{$list->id}}" ><a id="heartEntryIcon{{$list->id}}" class="dropdown-item" href="" onclick="">{{$list->name}}</a></li>
                            @endif
                        @endforeach
                        </ul>
                        <span class="ml-2" id="heartStatus"></span>
	                </li>
	            </ul>
			</div>
		</div>

		<!-- BUTTONS ROW 2 -->
		<div class="form-group" id="buttonRowReview">
			<span class="page-nav-buttons"><a class="btn btn-success btn-sm" role="button" href="#" onclick="event.preventDefault(); first()"><span class="glyphicon glyphicon-circle-arrow-up"></span>@LANG('ui.First')</a></span>
			<span class="page-nav-buttons"><a class="btn btn-success btn-sm" role="button" href="#" onclick="event.preventDefault(); prev()"><span class="glyphicon glyphicon-button-prev"></span>@LANG('ui.Prev')</a></span>
			<span class="page-nav-buttons"><a class="btn btn-success btn-sm" role="button" href="#" onclick="event.preventDefault(); next()">@LANG('ui.Next')<span class="glyphicon glyphicon-button-next"></span></a></span>
			<span class="page-nav-buttons"><a class="btn btn-success btn-sm" role="button" href="#" onclick="event.preventDefault(); last()">@LANG('ui.Last')<span class="glyphicon glyphicon-circle-arrow-down"></span></a></span>
			<span class="page-nav-buttons"><a class="btn btn-success btn-sm" role="button" href="#" onclick="event.preventDefault(); clear2()">@LANG('ui.Clear')</a></span>
		</div>

		<!-- CHECKBOX ROW -->
		<div class="form-group hide-for-mc">
			<div>
				<input type="checkbox" name="checkbox-type-answers" id="checkbox-type-answers" class="" onclick="quiz.typeAnswersClick()" />
				<label for="checkbox-type-answers" class="checkbox-big-label" onclick="quiz.typeAnswersClick()">@LANG('quiz.Type Answers')</label>
			</div>

			<div>
				<input type="checkbox" name="checkbox-flip" id="checkbox-flip" onclick="quiz.flip()" />
				<label for="checkbox-flip" class="checkbox-big-label">@LANG('quiz.Flip Question/Answer')</label>
			</div>

			<div>
				<input type="checkbox" name="checkbox-show-answers" id="checkbox-show-answers" onclick="quiz.showAnswersClick()" />
				<label for="checkbox-show-answers" class="checkbox-big-label">@LANG('quiz.Show Answers')</label>
			</div>
		</div>
	</div>

	</section>

	</div>
	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Start Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-start" class="quiz-panel text-center">

		<div class="quiz-panel-content">
			<h2>{{$parentTitle}}</h2>
			<!-- span style="margin:20px; font-size:75px;" class="glyphicon glyphicon-star-empty bright-blue-fg"></span -->
			<img style="margin:20px;" height="100" src="/img/quiz/quiz-start.jpg" />
			<h3>@LANG('quiz.Number of Questions')</h3>
			<h1 id="panelStartCount"></h1>
		</div>

		<div class="btn-panel-bottom pb-2">
			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); quiz.start()" id="button-start">@LANG('quiz.Start Review')</button>
			<a class="" role="" href="{{$returnPath}}"><button class="btn btn-lg btn-primary btn-quiz" >@LANG('ui.Quit')</button></a>
		</div>

	</div>

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Quiz Results Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-endofround" class="quiz-panel text-center">

		<div class="quiz-panel-content">
			<span class="hidden" id="panelResultsRoundBase">@LANG('quiz.End of Round')</span>
			<h1 id="panelResultsRound"></h1>
			<!-- span style="margin:20px; font-size:75px;" class="glyphicon glyphicon-stats bright-blue-fg"></span -->
			<img style="margin:20px;" height="100" src="/img/quiz/quiz-endofround.png" />
			<h3>@LANG('quiz.Correct Answers')</h3>
			<h1 id="panelResultsCount"></h1>
			<h3 id="panelResultsPercent"></h3>
			<p>Click Continue to answer to incorrect questions</p>
		</div>

		<div class="btn-panel-bottom pb-2">
			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); continueQuiz()" id="button-continue">@LANG('quiz.Continue')</button>
			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); stopQuiz()" id="button-stop">@LANG('ui.Quit')</button>
		</div>

	</div>

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- End of Flashcards Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-endofquiz" class="quiz-panel text-center">

		<div class="quiz-panel-content">
			<h1 class="" id="">@LANG('quiz.End of Review')</h1>
			<h4 id="panelEndofquizFinished">@LANG('quiz.All questions answered correctly')</h4>
			<p id="panelEndofquizStopped">@LANG('quiz.Review was stopped')</p>
			<img style="margin-bottom:20px;" width="100" src="/img/quiz/quiz-end.jpg" />
			<h3>@LANG('quiz.Scores by Round')</h3>
			<span class="hidden" id="roundsStart">@LANG('quiz.None Completed')</span>
			<span id="rounds"></span>
		</div>

		<div class="btn-panel-bottom pb-2">
		    @if ($showReload)
    			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); location.reload();" id="button-reload">@LANG('ui.Reload')</button>
            @endif
			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); startQuiz();" id="button-continue2">@LANG('ui.Repeat')</button>
			<a class="" role="" href="{{$returnPath}}"><button class="btn btn-lg btn-primary btn-quiz" >@LANG('ui.Quit')</button></a>
		</div>

	</div>

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Show Flashcards Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
    <div id="panel-show" style="clear:both; display:none; min-height:500px; overflow-y:auto;">
        <span id="translation" name="translation" class="">
            @component('shared.flashcards-view', ['records' => $records])@endcomponent
        </span>
    </div>

@endif

</div><!-- container -->

@endsection
