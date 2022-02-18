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
	loadOrder();
	quiz.showAnswersClick();
	quiz.typeAnswersClick();

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

function flipCard(e, remove = false)
{
	e.preventDefault();

	if (remove)
	{
	    quiz.setCorrect();
	}

	if ($("#flashcard-answer").is(":hidden"))
	{
		$('#flashcard-answer').show();
		$('#flashcard-extra').show();
		$('#button-remove').show();
	}
	else
	{
		$('#flashcard-answer').hide();
		$('#flashcard-extra').hide();
		$('#button-remove').hide();

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

	$("#stats").show();
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
	// nothing to do but still need for call from qnaBase
}

function nextAttemptORIGNOTUSED()
{
	clearTimeout(nextAttemptTimer);
	setButtonStates(RUNSTATE_ASKING);

	if (++curr < max)
	{
		loadQuestion();
	}
	else
	{
		quiz.showPanel(RUNSTATE_ENDOFQUIZ);
	}
}

function nextAttempt()
{
	clearTimeout(nextAttemptTimer);

	setButtonStates(RUNSTATE_ASKING);

	var done = false;
	var count = 0;
	while(!done)
	{
        // temp: set the current one to correct so quiz will end
	    quiz.setCorrect();

		curr++;

		// check if at the end of round
		if (curr >= max)
		{
			curr = 0;
			nbr = 0;
			score = (right / (right+wrong)) * 100;
			total = right + wrong;
			console.log('total: ' + total);
			if (total > 0)
			{
				results = '<p>' + quiz.quizTextRound + ' ' + round + ': ' + score.toFixed(2) + '% (' + right + '/' + total + ')</p>';
				if (round == 1)
					$("#rounds").text('');
				$("#rounds").append(results);
				//alert('End of Round, Starting next round');
				quiz.showPanel(RUNSTATE_ENDOFROUND);
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
		    console.log('load question');
			loadQuestion();
			done = true;
		}
		else if (count++ >= max)
		{
			// no wrong answers left
			//alert('Done, all answered correctly!!');
		    addHistory();
			quiz.showPanel(RUNSTATE_ENDOFQUIZ);
			resetQuiz();
			quiz.runState = RUNSTATE_ENDOFQUIZ;
			done = true;
		}

		if (count > 10000)
		{
			// break out just in care we're looping
			break;
		}
	}

    //todo: fix wrap around, remove questions, add history
	//console.log('curr: ' + curr);
	//console.log('max: ' + max);
}

