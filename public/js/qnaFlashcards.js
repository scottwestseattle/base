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

function flipCard(e)
{
	e.preventDefault(); 
	if ($("#flashcard-answer").is(":hidden"))
	{
		$('#flashcard-answer').show();
		$('#flashcard-extra').show();
	}
	else
	{
		$('#flashcard-answer').hide();
		$('#flashcard-extra').hide();
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

function nextAttempt()
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
