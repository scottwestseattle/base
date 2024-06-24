//-----------------------------------------------------------------------------
// QNA REVIEW
//-----------------------------------------------------------------------------

$( document ).ready(function() {

	//
	// set the checkboxes to their previous values
	//
	var checked = (localStorage.getItem('checkbox-hide-options') == 'true');
	$('#checkbox-hide-options').prop('checked', checked);

	checked = (localStorage.getItem('checkbox-flip-flashcards') == 'true');
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
	    right++;
	}
	else
	{
	    // this is from the "I got it wrong" button
	    wrong++;
	}

    quiz.setCorrect(correct);

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

function showQuestion() //Flashcard()
{
    // are we doing flashcards or mc?
	var extra = getRule();
    if (extra !== null && extra.length > 0) // extra hold the MC options
	{
	    $("#show-multiple-choice").show();
	    $("#show-flashcards").hide();
        showQuestionMultipleChoice();
	}
	else // show flashcards
	{
	    $("#show-multiple-choice").hide();
	    $("#show-flashcards").show();
	}

	var q = getQuestion();
	var a = getAnswer();

    //
    // set up 'Go To Entry' and 'Delete Entry' links
    //
    var id = quiz.qna[quiz.qna[curr].order].id;
    var href = '/' + quiz.locale + '/definitions/edit-or-show/' + id;
	$('#goToEntry').attr("href", href);

    //
    // set up Delete for current record
    //
    var hrefDelete = '/' + quiz.locale + '/definitions/delete/' + id;
    var onclick = 'event.preventDefault(); ajaxexec("' + hrefDelete + '"); $("#deleteStatus").text("deleted")';
	$('#deleteEntryIcon').attr("onclick", onclick);

    //
    // set up Heart for current record
    //
    var hrefUnheart = '/' + quiz.locale + '/definitions/set-favorite-list/' + id + '/' + quiz.programId + '/0';
    var unheart = 'event.preventDefault(); ajaxexec("' + hrefUnheart + '"); $("#heartStatus").text("unhearted")';
	$('#unheartEntryIcon').attr("onclick", unheart);

    // set the heart li's; have to loop through each item, get the 'to id' from the 'data' and set the onclick url dynamically
    var favs = $("#favs li");
    favs.each(function(idx, li) {
        var container = $(li);
        var favId = container.data('tagid'); // the 'to id' is stored on the 'li' element
        if (favId !== 'undefined') // skip the unheart which has no 'to id'
        {
            var hrefHeart = '/' + quiz.locale + '/definitions/set-favorite-list/' + id + '/' + quiz.programId + '/' + favId;
            var heart = 'event.preventDefault(); ajaxexec("' + hrefHeart + '"); $("#heartStatus").text("hearted")';
            $('#heartEntryIcon' + favId).attr("onclick", heart);
            //console.log('heart url: ' + heart);
        }
    });

    //
	// show question
	//
	$(".prompt").html(q);
	$("#flashcard-answer").html(a);
	$("#flashcard-extra").html(extra);

	quiz.setAlertPrompt(quiz.promptQuestion, COLOR_QUESTION_PROMPT);

	$("#statsRuntime").show();
}

function showQuestionMultipleChoice()
{
	clear();
	var q = getQuestion();
	var a = getAnswer();
	var currIndex = quiz.qna[curr].order;
	var currQuestion = quiz.qna[currIndex];
	var choices = currQuestion.choices;
    console.log(choices);

	var debugOn = false;

	// show question
	$("#prompt").html(getQuestion());

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
	$(".btn-quiz-mc3").hide();

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

	$("#statsRuntime").show();
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

