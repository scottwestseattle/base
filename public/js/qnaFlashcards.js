//-----------------------------------------------------------------------------
// QNA REVIEW
//-----------------------------------------------------------------------------

$( document ).ready(function() {

	//
	// set the checkboxes to their previous values
	//
	var checked = (localStorage.getItem('checkbox-hide-options') == 'true');
	$('#checkbox-hide-options').prop('checked', checked);

	checked = (localStorage.getItem('checkbox-flip') == 'true');
	$('#checkbox-flip').prop('checked', checked);

	checked = (localStorage.getItem('checkbox-use-definition') == 'true');
	$('#checkbox-use-definition').prop('checked', checked);

	// do other stuff
	setButtonStates(RUNSTATE_START);
	quiz.setControlStates();
	loadData();
	quiz.showAnswersClick();
	quiz.typeAnswersClick();

    //
    // this will override the random setting based on the last state of the checkbox
    //
	checked = (localStorage.getItem('checkbox-random') == 'true');
	$('#checkbox-random').prop('checked', checked);
    updateRandom(/* reloadOrder = */ false); // load order will be called again after this

	$("#checkbox-type-answers").prop('checked', startWithTypeAnswers());

	quiz.showPanel();

	quiz.start();
});

function getExtra()
{
	var rc = null;

	index = quiz.qna[curr].order;
	rc = quiz.qna[index].extra;

	return rc;

}

function updateScoreCount(correct)
{
	if (correct)
	{
	    // default is correct (when it's clicked after answer is shown)
	    quiz.setCorrect(true);
	    right++;
	}
	else
	{
	    // this is from the "I got it wrong" button
	    wrong++;
	}

	//console.log('right: ' + right);
	//console.log('wrong: ' + wrong);
	//console.log('total: ' + (right + wrong));
}

function flipCard(e, correct = true)
{
	e.preventDefault();

	if ($("#flashcard-answer").is(":hidden"))
	{
	    //
	    // show the answer
	    //
		$('#flashcard-answer').show();
		$('#flashcard-extra').show();
		$('#button-remove').show();
		$('#button-repeat').show();
	}
	else
	{
	    //
	    // hide answer, update score, and load next question
	    //
		$('#flashcard-answer').hide();
		$('#flashcard-extra').hide();
		$('#button-remove').hide();
		$('#button-repeat').hide();

        updateScoreCount(correct);

		nextAttempt();
	}
}

function showQuestion()
{
	var q = getQuestion();
	var a = getAnswer();
	var extra = getExtra();

	// show question
	$("#prompt").html(q);
	$("#flashcard-answer").html(a);
	$("#flashcard-extra").html(extra);

	quiz.setAlertPrompt(quiz.promptQuestion, COLOR_QUESTION_PROMPT);

	$("#statsRuntime").show();
}

function restartQuiz()
{
	quiz.showPanel();
	resetQuiz();
	quiz.runState = RUNSTATE_ASKING;
	loadQuestion();
}

function resetEndPanels()
{
	$("#statsRuntime").hide();
	$("#panelEndofquizFinished").show();
	$("#panelEndofquizStopped").hide();
}

function nextAttempt()
{
	clearTimeout(nextAttemptTimer);

	setButtonStates(RUNSTATE_ASKING);

	var done = false;
	var count = 0;
	while(!done)
	{
		curr++;

		// check if at the end of round
		if (curr >= max)
		{
			curr = 0;
			nbr = 0;
			score = (right / (right+wrong)) * 100;
			total = right + wrong;
			if (total > 0)
			{
				results = '<p>' + quiz.quizTextRound + ' ' + round + ': ' + score.toFixed(2) + '% (' + right + '/' + total + ')</p>';
				if (round == 1)
					$("#rounds").text('');
				$("#rounds").append(results);

				$state = (wrong == 0) ? RUNSTATE_ENDOFQUIZ : RUNSTATE_ENDOFROUND;
				quiz.showPanel($state);
			}
			else
			{
				//alert('End of Round???');
			}

			//alert('End of Round ' + round + ': ' + score.toFixed(2) + '% (' + right + ' of ' + (right+wrong) + ')');

			round++;
			statsMax = wrong;
			right = 0;
			wrong = 0;
		}

		// if this question has not been answered correctly yet
		if (!quiz.qna[quiz.qna[curr].order].correct)
		{
			loadQuestion();
			done = true;
		}
		else if (count++ >= max)
		{
			// no wrong answers left
			//alert('Done, all answered correctly!!');
			//quiz.showPanel(RUNSTATE_ENDOFQUIZ);
			//resetQuiz();
			quiz.runState = RUNSTATE_ENDOFQUIZ;
			done = true;

			// update user's history
			addHistory();
		}

		if (count > 10000)
		{
			// break out just in care we're looping
			break;
		}
	}
}

