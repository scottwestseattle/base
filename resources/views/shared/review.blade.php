@extends('layouts.review')
@section('title', __('proj.Review'))
@section('content')
@php
    $quizCount = isset($quizCount) ? $quizCount : $sentenceCount;
@endphp
<!-------------------------------------------------------->
<!-- Add misc data needed by the JS during runtime -->
<!-------------------------------------------------------->
<div class="data-misc"
	data-touchpath="{{(isset($touchPath) ? $touchPath : '')}}"
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
	data-locale="{{app()->getLocale()}}"
	data-random="{{$random}}"
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
	<div style="margin-top: 5px;">
		<div id="statsRuntime">
            <!-------------------------------------------------------->
            <!-- Top Return Button -->
            <!-------------------------------------------------------->
			<div class="middle mr-1 mb-1">
                <a href="/"><span style="margin-right:3px;" class="glyphicon glyphicon-home"></span></a>
                <a href="{{$returnPath}}"><button type="button" class="btn btn-xs btn-primary mb-1"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-up"></span>{{trans_choice('ui.Return', 1)}}</button></button></a>
			</div>

            <!-------------------------------------------------------->
            <!-- Run-time Stats -->
            <!-------------------------------------------------------->
			<div class="middle mb-2">
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

    <section class="quizSection" id='sectionQna'>

	<!-------------------------------------------------------->
	<!-- Instructions -->
	<!-------------------------------------------------------->
	<div class="text-center" id="" style="font-size: 1em; margin-bottom:10px;">
		<!-------------------------------------------------------->
		<!-- SHOW Question prompt and results RIGHT/WRONG -->
		<!-------------------------------------------------------->
		<span id="alertPrompt"></span>
	</div>

	<!-------------------------------------------------------->
	<!-- QUESTION -->
	<!-------------------------------------------------------->

	<div id="question-graphics" class="text-center" style="font-size: {{$settings['options']['font-size']}}; margin-bottom:20px;">
		<span id="prompt"></span>
	</div>

	<!-------------------------------------------------------->
	<!-- ANSWER -->
	<!-------------------------------------------------------->

	<div class="">
		<fieldset id="runtimeFields">

		<div class="text-center">
			<!-------------------------------------------------------->
			<!-- TEXTBOX TO ENTER ANSWER -->
			<!-------------------------------------------------------->
			<input class="form-control" autocomplete="off" type="text" name="answer" id="attemptInput" onkeypress="onKeypress(event)" />

			<!-------------------------------------------------------->
			<!-- SPACE TO SHOW SCORED ANSWER -->
			<!-------------------------------------------------------->
			<div style="display: none; padding: 10px 0; font-size: {{$settings['options']['font-size']}}; min-height: 70px; margin-top: 20px;" id="answer-show-div"></div>
		</div>

		<!-------------------------------------------------------->
		<!-- ANSWER OPTION BUTTONS  -->
		<!-------------------------------------------------------->
		<div style="width:100%;" id="optionButtons">
			<div><button id="0" onclick="checkAnswerFromButtonClick(event)" class="btn btn-primary btn-quiz-mc3" style="display:none;"></button></div>
			<div><button id="1" onclick="checkAnswerFromButtonClick(event)" class="btn btn-primary btn-quiz-mc3" style="display:none;"></button></div>
			<div><button id="2" onclick="checkAnswerFromButtonClick(event)" class="btn btn-primary btn-quiz-mc3" style="display:none;"></button></div>
			<div><button id="3" onclick="checkAnswerFromButtonClick(event)" class="btn btn-primary btn-quiz-mc3" style="display:none;"></button></div>
			<div><button id="4" onclick="checkAnswerFromButtonClick(event)" class="btn btn-primary btn-quiz-mc3" style="display:none;"></button></div>
		</div>

		</fieldset>

	<!----------------------------------------------------------------------------->
	<!-- CONTROL BUTTONS -->
	<!----------------------------------------------------------------------------->

		<!-- BUTTONS ROW 1 -->

		<div class="btn-panel-bottom pb-2">
			<button class="btn btn-success btn-quiz" onclick="event.preventDefault(); nextAttempt()" id="button-next-attempt">@LANG('ui.Next')</button>
			<input class="btn btn-default btn-quiz " type="button" value="@LANG('quiz.I KNOW IT') (Alt+k)" onclick="checkAnswer(2)" id="button-know" style="display: default; background-color: green; color: white;">
			<input class="btn btn-default btn-quiz" type="button" value="@LANG('quiz.I DONT KNOW') (Alt+d)" onclick="checkAnswer(3)" id="button-dont-know" style="display: none; background-color: red; color: white;">
			<input class="btn btn-default btn-quiz" type="button" value="@LANG('quiz.Change to Wrong') (Alt+c)" onclick="override()" id="button-override" style="display: none;">
		</div>

		<div class="form-group">
			<button class="btn btn-primary btn-quiz" onclick="event.preventDefault(); checkAnswer(1)" id="button-check-answer">@LANG('quiz.Check Typed Answer')</button>
			<button class="btn btn-warning btn-quiz" onclick="event.preventDefault(); stopQuiz()" id="button-stop">@LANG('quiz.Stop Review')</button>
			<button class="btn btn-primary btn-quiz" onclick="event.preventDefault(); showAnswerOptionButtons()" id="button-show-options">@LANG('quiz.Show Choices')</button>
			<button class="btn btn-success btn-quiz" onclick="event.preventDefault(); showAnswer()" id="button-show-answer">@LANG('quiz.Show Answer')</button>
			<div class="mt-2 ml-1">
				<input type="checkbox" name="checkbox-hide-options" id="checkbox-hide-options" onclick="displayAnswerButtons()" />
				<label for="checkbox-hide-options" class="checkbox-xs" onclick="displayAnswerButtons()">@LANG('quiz.Hide choices before answering')</label>
			</div>
			<div class="mt-1 ml-1">
				<input type="checkbox" name="checkbox-flip" id="checkbox-flip" onclick="reloadQuestion('checkbox-flip-review');" />
				<label for="checkbox-flip" class="checkbox-xs" onclick="reloadQuestion('checkbox-flip-review');">@LANG('quiz.Reverse question and answer')</label>
			</div>
			<div class="small-thin-text">
                <a target='_blank' href="/definitions/add/{{'here'}}">Add Snippet</a>
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
		</div>

		<div class="btn-panel-bottom pb-2">
			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); continueQuiz()" id="button-continue">@LANG('quiz.Continue')</button>
			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); stopQuiz()" id="button-stop">@LANG('ui.Quit')</button>
		</div>

	</div>

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- End of Quiz Panel -->
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
			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); location.reload();" id="button-continue2">@LANG('ui.Reload')</button>
			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); startQuiz();" id="button-continue2">@LANG('ui.Restart')</button>
			<a class="" role="" href="{{$returnPath}}"><button class="btn btn-lg btn-primary btn-quiz" >@LANG('ui.Quit')</button></a>
		</div>

	</div>

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Debug Info -->
	<!---------------------------------------------------------------------------------------------------------------->
@if (false) // debug dump
	<div>
	@foreach($records as $rec)
		<p>{!!$rec['q']!!}</p>
	@endforeach
	</div>
@endif

@endif

</div><!-- container -->

@endsection
