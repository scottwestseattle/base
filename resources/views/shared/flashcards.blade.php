@extends('layouts.review')

@section('content')

<!-------------------------------------------------------->
<!-- Add misc data needed by the JS during runtime -->
<!-------------------------------------------------------->
<div class="data-misc"
	data-max="{{$sentenceCount}}"
	data-prompt="@LANG('lesson.' . $settings['options']['prompt'])"
	data-prompt-reverse="@LANG('lesson.' . $settings['options']['prompt-reverse'])"
	data-question-count="{{$settings['options']['question-count']}}"
	data-quiztext-round="@LANG('content.Round')"
	data-quiztext-correct="@LANG('content.Correct')"
	data-quiztext-question="@LANG('content.Question')"
	data-ismc="{{$isMc}}"
	data-quiztext-of="@LANG('content.of')"
	data-quiztext-correct-answer="@LANG('content.Correct!')"
	data-quiztext-wrong-answer="@LANG('content.Wrong!')"
	data-quiztext-marked-wrong="@LANG('content.Answer marked as wrong')"	
	data-quiztext-override-correct="@LANG('content.Change to Correct')"
	data-quiztext-override-wrong="@LANG('content.Change to Wrong')"
	data-quiztext-score-changed="@LANG('content.Score Changed')"
@if (false)
	data-quiztype="{{$record->type_flag}}"
	data-lessonid="{{$record->id}}"
@endif
	data-touchpath="{{(isset($touchPath) ? $touchPath : '')}}"
></div>

	<!-------------------------------------------------------->
	<!-- Add the q and a records -->
	<!-------------------------------------------------------->
@foreach($records as $rec)
	<div class="data-qna"
	    data-question="{{$rec['q']}}"
	    data-answer="{{$rec['a']}}"
	    data-definition="{{$rec['definition']}}"
	    data-extra="{{$rec['extra']}}"
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

		<!-------------------------------------------------------->
		<!-- Top Return Button -->
		<!-------------------------------------------------------->
		<div style="float:left; margin: 0 5px 0 0;">
			<span style="font-size:1.3em;" class=""><a class="" role="" href="{{$returnPath}}"><span class="glyphicon glyphicon-button-back-to"></span></a></span>
		</div>

		<!-------------------------------------------------------->
		<!-- Run-time Stats -->
		<!-------------------------------------------------------->		
		<div id="stats">
			<div class="middle mt-1 mr-1"><a href="{{$returnPath}}"><span class="glyphicon glyphReaderReturn glyphicon-circle-arrow-up"></span></a></div>
			<span id="statsCount" class="mr-2"></span>
			<span id="statsScore" class="hidden"></span>
			<span id="statsAlert"></span><!-- what is this? -->
		</div>
		

	</div>

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Quiz Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-quiz" style="" class="quiz-panel">

	@if (count($records) > 0)

	<section class="" id='sectionQna'>

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

	<div class="text-center" style="xbackground-color:gray;">
	<div class="card card-flashcard card-blue text-center" style="font-size: {{$settings['options']['font-size']}};">
		<a href="" onclick="flipCard(event);">
			<div class="card-header">
				<div id="prompt" class="card-text"></div>
			</div>
			<div class="card-body">
				<p id="flashcard-answer" class="card-text hidden"></p>
				<p id="flashcard-extra" class="large-text hidden"></p>
			</div>
		</a>
	</div>	
	</div>

	<!-------------------------------------------------------->
	<!-- ANSWER -->
	<!-------------------------------------------------------->

	<div class="">
		<fieldset id="runtimeFields">

	<!----------------------------------------------------------------------------->
	<!-- CONTROL BUTTONS -->
	<!----------------------------------------------------------------------------->

		<!-- BUTTONS ROW 1 -->

		<div class="btn-panel-bottom pb-2">
			<button class="btn btn-success btn-quiz" onclick="event.preventDefault(); nextAttempt()" id="button-next-attempt">@LANG('ui.Next')</button>
			<input class="btn btn-default btn-quiz " type="button" value="@LANG('content.I KNOW IT') (Alt+k)" onclick="checkAnswer(2)" id="button-know" style="display: default; background-color: green; color: white;">
			<input class="btn btn-default btn-quiz" type="button" value="@LANG('content.I DONT KNOW') (Alt+d)" onclick="checkAnswer(3)" id="button-dont-know" style="display: none; background-color: red; color: white;">
			<input class="btn btn-default btn-quiz" type="button" value="@LANG('content.Change to Wrong') (Alt+c)" onclick="override()" id="button-override" style="display: none;">
		</div>

		<div class="form-group">
			<button class="btn btn-primary btn-quiz" onclick="event.preventDefault(); checkAnswer(1)" id="button-check-answer">@LANG('content.Check Typed Answer')</button>
			<div class="mt-1 ml-1">
				<input type="checkbox" name="checkbox-flip" id="checkbox-flip" onclick="reloadQuestion();" />
				<label for="checkbox-flip" class="checkbox-xs" onclick="reloadQuestion();">@LANG('content.Reverse question and answer')</label>
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
				<label for="checkbox-type-answers" class="checkbox-big-label" onclick="quiz.typeAnswersClick()">@LANG('content.Type Answers')</label>
			</div>

			<div>
				<input type="checkbox" name="checkbox-flip" id="checkbox-flip" onclick="quiz.flip()" />
				<label for="checkbox-flip" class="checkbox-big-label">@LANG('content.Flip Question/Answer')</label>
			</div>

			<div>
				<input type="checkbox" name="checkbox-show-answers" id="checkbox-show-answers" onclick="quiz.showAnswersClick()" />
				<label for="checkbox-show-answers" class="checkbox-big-label">@LANG('content.Show Answers')</label>
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
			<img style="margin:20px;" height="100" src="/img/quiz-start.jpg" />
			<h3>@LANG('content.Number of Questions')</h3>
			<h1 id="panelStartCount"></h1>
		</div>

		<div class="btn-panel-bottom pb-2">
			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); quiz.start()" id="button-start">@LANG('content.Start Review')</button>
			<a class="" role="" href="{{$returnPath}}"><button class="btn btn-lg btn-primary btn-quiz" >@LANG('ui.Quit')</button></a>			
		</div>

	</div>

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- End of Flashcards Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-endofquiz" class="quiz-panel text-center">

		<div class="quiz-panel-content">
			<h1 class="" id="">@LANG('qna.End of Questions')</h1>
			<img style="margin-bottom:20px;" width="100" src="/img/quiz-end.jpg" />
			<h3>@LANG('qna.Click below to restart')</h3>
		</div>

		<div class="btn-panel-bottom pb-2">
			<button class="btn btn-lg btn-primary btn-quiz" onclick="event.preventDefault(); restartQuiz();" id="button-continue2">@LANG('ui.Restart')</button>
			<a class="" role="" href="{{$returnPath}}"><button class="btn btn-lg btn-primary btn-quiz" >@LANG('ui.Quit')</button></a>
		</div>

	</div>

@endif

</div><!-- container -->

@endsection
