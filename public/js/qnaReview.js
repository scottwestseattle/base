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

	$("#checkbox-type-answers").prop('checked', startWithTypeAnswers());

	quiz.showPanel();

	quiz.start();
});

function showQuestionCustom()
{
	var q = getQuestion();

	// show question
	$("#prompt").html(q);
}

//sbw
function showQuestion()
{
	clear();
	var q = getQuestion();
	var a = getAnswer();
	var currIndex = quiz.qna[curr].order;
	var currQuestion = quiz.qna[currIndex];
	var choices = currQuestion.choices;
	var debugOn = false;

	showQuestionCustom();

	// shows or hides answer option buttons according to checkbox
	displayAnswerButtons();

	// new way where buttons are in html and configured from here
	var answers = new Array();
    var choiceCnt = 0;
    var choicesArray = new Array();
    var totalAnswers = 0;

	if (choices) // if answers are provided in the question text like: "El [es, está] bravo." and they arrive here as: es|está
	{
	    //
	    // this way uses the embedded answers
	    //
	    //console.log('using embedded answers');
	    choicesArray = choices.split("|");
        choiceCnt = choicesArray.length;
        totalAnswers = choiceCnt;
	}
	else
	{
	    //
	    // this way uses random answers from all the other questions
	    //
        choiceCnt = Math.min(quiz.qna.length, 5);
        totalAnswers = quiz.qna.length;
	}

    // fill up the array with random answer indexes
    for (var i = 0; i < choiceCnt; i++) // start at one because we've already added the correct answer
    {
        var rnd = Math.floor(Math.random() * totalAnswers);

        // if it's not the correct answer AND it's not already in the answers list
        if (!answers.includes(rnd))
        {
            // not in array yet, add it
            answers.push(rnd);
        }
        else
        {
            // continue from the random position until we find an unused answer
            var loop = 0;
            while(loop < totalAnswers) // don't loop forever
            {
                rnd++;
                if (rnd >= choiceCnt)
                    rnd = 0; // wrap to the beginning and keep looking

                // if not in the answers list, add it
                if (!answers.includes(rnd))
                {
                    answers.push(rnd);
                    break;
                }

                loop++;
            }
        }
    }

    if (choices)
    {
        // answer were embedded so correct answer is already in the list
    }
    else
    {
        // now lay in the correct answer randomly if it's not already in the array
        if (!answers.includes(currIndex))
        {
            var correctButton = Math.floor(Math.random() * choiceCnt);
            answers[correctButton] = currIndex;
        }
    }

	if (false)
	{
		console.log('choices: ' + choices);
		console.log('choiceCnt: ' + choiceCnt);
		console.log('currIndex: ' + currIndex);
		console.log('correct button: ' + correctButton);
		answers.forEach(function (item, index, arr) {
			console.log('random array: ' + index + ', item: ' + item + ', ans: ' +  quiz.qna[item].a);
		});
	}

	// reset the buttons
	$(".btn-quiz-mc3").removeClass('btn-right');
	$(".btn-quiz-mc3").removeClass('btn-right-show');
	$(".btn-quiz-mc3").removeClass('btn-wrong');
	$(".btn-quiz-mc3").removeClass('btn-chosen');
	$(".btn-quiz-mc3").css('background-color', '#2fa360');
	$(".btn-quiz-mc3").css('border-color', '#2d995b');
	$(".btn-quiz-mc3").css('color', 'white');

    //
    // now update the static view buttons with the answers using the unique list of
    // random indexes that we've created
    //
	answers.forEach(function (item, index, arr) {
		var btn = '#' + index;

        if (choices)
        {
            // set the button text
		    var text = choicesArray[item]; // quiz.qna[item].a;
		    $(btn).html(text);

            if (false)
            {
                console.log('item = ' + item);
                console.log('text = ' + text);
                console.log('a = ' + a);
            }

            if (text.localeCompare(a) == 0)
                $(btn).addClass('btn-right');
            else
               $(btn).addClass('btn-wrong');
        }
        else
        {
            // set the button text
	    	var text = getAnswer(item); // quiz.qna[item].a;
		    $(btn).html(text);

            // add a class so we know the right answer
            if (item == currIndex)
                $(btn).addClass('btn-right');
            else
                $(btn).addClass('btn-wrong');
        }

		// buttons start as hidden in case we are using less than the max (5)
		// only show the ones we are using so we're not lugging around dead empty buttons
		$(btn).show();
	});

	// show answer
	if ($("#checkbox-show").prop('checked'))
	{
		$("#answer-show").html(a);
		$("#answer-show").val(a);
	}

	// show prompt
	$("#promptQuestion").text(quiz.promptQuestion + " ");

	quiz.setAlertPrompt(quiz.promptQuestion, COLOR_QUESTION_PROMPT);

	$("#stats").show();
}

function continueQuiz()
{
	// if end of round but not end of quiz, keep asking
	if (quiz.runState == RUNSTATE_ENDOFROUND)
		quiz.runState = RUNSTATE_ASKING;

	quiz.showPanel();
}

function showAnswerOptionButtons()
{
	// use visibility instead of show/hide to keep the spacing
	$("#optionButtons").css('visibility', 'visible');
	$("#button-show-options").hide();
	$("#button-show-answer").show();
}

function displayAnswerButtons()
{
	if ($("#checkbox-hide-options").prop('checked'))
	{
		// use visibility instead of show/hide to keep the spacing
		$("#optionButtons").css('visibility', 'hidden');
		$("#button-show-options").show();
		$("#button-show-answer").hide();
	}
	else
	{
		$("#optionButtons").css('visibility', 'visible');
		$("#button-show-options").hide();
		$("#button-show-answer").show();
	}

	var checked = $('#checkbox-hide-options').prop('checked') ? 'true' : '';
	localStorage.setItem('checkbox-hide-options', checked);
}

function showAnswer()
{
	$("#button-show-answer").hide();
	var id = $(".btn-right").attr('id');
	$('.btn-right').addClass('btn-right-show');
	checkAnswerFromButton(id, true);
}

function resetEndPanels()
{
	$("#stats").hide();
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
