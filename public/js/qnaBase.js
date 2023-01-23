//-----------------------------------------------------------------------------
// QNA Base with all of the shared functions
//-----------------------------------------------------------------------------

//
// Constants
//
const RUNSTATE_START = 1;
const RUNSTATE_ASKING = 2;
const RUNSTATE_CHECKING = 3;
const RUNSTATE_ENDOFROUND = 4;
const RUNSTATE_ENDOFQUIZ = 5;

const CHECKANSWER_NORMAL = 1;
const CHECKANSWER_KNOW = 2;
const CHECKANSWER_DONTKNOW = 3;
const CHECKANSWER_FROMBUTTON = 4;

const SCORE_NOTSET = 0;
const SCORE_CORRECT = 1;
const SCORE_WRONG = 2;

const COLOR_QUESTION_PROMPT = '#4682b4'; // steelblue

//
// numbers
//
var wrong = 0;
var right = 0;
var round = 1;
var curr = 0;
var nbr = 0;

//
// max number of qna
//
var max = 0;
var quizCount = 0;
var statsMax = 0;

var nextAttemptTimer = null;

$(document).keydown(function(event) {

	//alert(event.altKey);

	if (event.altKey )
	{

		if (event.which == 75 || event.which == 107) // alt-k
		{
			$("#button-know").click()
			event.preventDefault();
			event.stopPropagation();
		}
		else if (event.which == 68 || event.which == 100) // alt-d
		{
			$("#button-dont-know").click()
			event.preventDefault();
			event.stopPropagation();
		}
		else if (event.which == 99 || event.which == 67) // alt-c
		{
			$("#button-override").click();
			event.preventDefault();
			event.stopPropagation();
		}
	}

});

//
// quiz class
//
function quiz() {

	this.qna = [];

	// options
	this._flip = false;
	this.promptQuestionNormal = ''; // loaded after ready
	this.promptQuestionReverse = ''; // loaded after ready
	this.promptQuestion = ''; // set to appropriate prompt: normal or reverse
	this.lastScore = SCORE_NOTSET;
	this.runState = RUNSTATE_START;

	//new:
	this.isMc = 0;
	this.quizTextRound = 'not set';
	this.quizTextCorrect = 'not set';
	this.quizTextOf = 'not set';
	this.quizTextQuestion = 'not set';
	this.quizTextCorrectAnswer = 'not set'; // Correct! <- separate for the exclamations in Spanish
	this.quizTextMarkedWrong = 'not set';
	this.quizTextWrongAnswer = 'not set';	// Wrong!
	this.quizTextOverrideCorrect = 'not set';
	this.quizTextOverrideWrong = 'not set';
	this.quizTextScoreChanged = 'not set';
	//this.lessonId = 'not set';
	this.random = true;

    // for History
    this.programName = 'not set';
    this.programId = 0;
    this.programType = 0;
    this.programSubType = 0;
    this.programAction = 0;
    this.sessionName = 'not set';
    this.sessionId = 0;
    this.historyPath = 'not set';
    this.historyRoute = 'not set';
    this.touchPath = 'not set';

    this.showParameters = function()
    {
        if (false)
        {
            console.log("historyPath: " + this.historyPath);
            console.log("programName: " + this.programName);
            console.log("programId: " + this.programId);
            console.log("programType: " + this.programType);
            console.log("programSybType: " + this.programSubType);
            console.log("sessionName: " + this.sessionName);
            console.log("sessionId: " + this.sessionId);
            console.log("historyRoute: " + this.historyRoute);
        }
    }

	this.getQuestionId = function(index) {
		return this.qna[this.qna[index].order].id;
	}

	this.question = function(index) {
		return this.qna[this.qna[index].order];
	}

	this.setControlStates = function() {
		//$("#button-start").focus();
	}

	this.showPanel = function(state = null) {

		$(".quiz-panel").hide();

		if (state == null)
			state = this.runState;

		switch(state)
		{
			case RUNSTATE_ENDOFQUIZ:
				$("#panel-endofquiz").show();
				break;

			case RUNSTATE_ENDOFROUND:
			{
				$("#panel-endofround").show();

				roundText = $("#panelResultsRoundBase").text() + ' ' + round;
				count = right + '/' + total;
				var fScore = score.toFixed(2);
				percent = fScore + '%';

				$("#panelResultsRound").text(roundText);
				$("#panelResultsPercent").text(percent);
				$("#panelResultsCount").text(count);

				// log the quiz round
				//if (parseInt(round) == 1)
				//	ajaxexec('/lessons/log-quiz/' + this.lessonId + '/' + fScore);

				break;
			}
			case RUNSTATE_START:
				$("#panel-start").show();
				$("#panelStartCount").text(this.qna.length);
				break;
			default:
				$("#panel-quiz").show();
				this.setFocus();
				break;
		}

	}

	this.setFocus = function() {
	}

	this.flipped = function() {
		return $('#checkbox-flip').prop('checked');
	}

	this.useDefinition = function() {
		return $("#checkbox-use-definition").prop("checked");
	}

	//review: not used
	this.flip = function() {
		this._flip = !this._flip;
		this.promptQuestion = (this._flip ? this.promptQuestionReverse : this.promptQuestionNormal);
		showQuestion();
	}

	this.start = function() {
		$("#rounds").text($("#roundsStart").text());
		resetQuiz();
		showQuestion();
		nbr = 1;
		updateScore();

		setButtonStates(RUNSTATE_ASKING);

		this.showPanel();
	}

	this.showOverrideButton = function(show, label)
	{
		$("#button-override").prop('disabled', !show);

		if (label != null)
			$("#button-override").val(label);

		if (!show)
		{
			$("#button-override").hide();
			$("#button-override").css('background-color', 'white');
		}
		else
		{
			$("#button-override").show();
			$("#button-override").css('background-color', 'yellow');
		}
	}

	this.typeAnswersClick = function()
	{
		setButtonStates(this.runState);

		var typeAnswers = $("#checkbox-type-answers").prop('checked');

		if (this.runState != RUNSTATE_START)
		{
			if (typeAnswers)
			{
				$("#attemptInput").show();
				$("#attemptInput").focus();
			}
			else
			{
				$("#attemptInput").hide();
			}
		}

		if (this.runState == RUNSTATE_ASKING)
		{
			quiz.setAlertPrompt(quiz.promptQuestion, COLOR_QUESTION_PROMPT);
		}
	}

	this.showAnswersClick = function() {

		var showAnswers = $("#checkbox-show").prop('checked');
		var answer = '';
		if (showAnswers)
		{
			$("#buttonRowReview").css('display', 'default');
			answer = getAnswer();
		}
		else
		{
			$("#buttonRowReview").css('display', 'none');
		}

		$("#answer-show").val(answer);
		$("#answer-show").html(answer);
	}

	this.showList = function() {
		if ($("#showAllLink").html() == "Show All Questions")
		{
			$("#showAllLink").html("Show Quiz");
			$(".quizSection").hide();
			$("#sectionReview").show();
		}
		else
		{
			$("#showAllLink").html("Show All Questions");
			$(".quizSection").show();
			$("#sectionReview").hide();
		}
	}

	this.setAlertPrompt = function(text, color, bold = false) {
		$("#alertPrompt").html(text);
		$("#alertPrompt").css('color', color);
		$("#alertPrompt").css('font-weight', bold ? 'bold' : 'normal');
	}

	this.setCorrect = function(correct) {
	    var q = this.qna[this.qna[curr].order];
		q.correct = correct;
		touch(q, correct);
    }
}

var quiz = new quiz();

function loadData()
{
	//console.log('>>> loadData');

	//
	// load qna arrays from the html tag 'data-' attributes, for example: data-question, data-answer, data-prompt
	//
	var i = 0;
	$('.data-qna').each(function() {
        var container = $(this);
        var service = container.data('title');

		var question = container.data('question');
		var answer = container.data('answer');
		var choices = container.data('choices');
		var def = container.data('definition');
		var extra = container.data('extra');
		var options = container.data('options'); // mc options
		var id = container.data('id');
		var wordId = container.data('wid');

		// add the record
		quiz.qna[i] = {
		    q:question.toString(),
		    a:answer.toString(),
		    choices:(choices) ? choices.toString() : null,
			definition:def.toString(),
			extra:extra.toString(),
		    id:id.toString(),
		    options:options.toString(),
		    order:0,
		    correct:false
		};

		//alert(quiz.qna[i].id);
		//if (i == 0) alert(quiz.qna[i].q);

		i++;
    });

	//
	// load misc variables
	//
	$('.data-misc').each(function() {
        var container = $(this);

		max = container.data('max');             // total questions available

		// if user wants to do less than the total number of questions available
		// done here so we can always get a subset of the available questions and
		// not just the same subset every time
		quizCount = container.data('quizcount');
		quiz.promptQuestionNormal = container.data('prompt');
		quiz.promptQuestionReverse = container.data('prompt-reverse');
		quiz.promptQuestion = quiz.promptQuestionNormal;

        // for History updates
		quiz.programType = container.data('programtype');
		quiz.programSubType = container.data('programsubtype');
		quiz.programAction = container.data('programaction');
		quiz.programName = container.data('programname');
		quiz.programId = container.data('programid');
		quiz.sessionName = container.data('sessionname');
		quiz.sessionId = container.data('sessionid');
		quiz.historyPath = container.data('historypath');
		quiz.historyRoute = container.data('historyroute');
		quiz.touchPath = container.data('touchpath');
        quiz.showParameters();

		// new settings
		quiz.isMc = container.data('ismc');
		quiz.quizTextRound = container.data('quiztext-round');
		quiz.quizTextCorrect = container.data('quiztext-correct');
		quiz.quizTextOf = container.data('quiztext-of');
		quiz.quizTextQuestion = container.data('quiztext-question');
		quiz.quizTextCorrectAnswer = container.data('quiztext-correct-answer');
		quiz.quizTextMarkedWrong =  container.data('quiztext-marked-wrong');
		quiz.quizTextWrongAnswer = container.data('quiztext-wrong-answer');
		quiz.quizTextOverrideCorrect = container.data('quiztext-override-correct') + " (Alt+c)";
		quiz.quizTextOverrideWrong = container.data('quiztext-override-wrong') + " (Alt+c)";
		quiz.quizTextScoreChanged = container.data('quiztext-score-changed');
		//quiz.lessonId = container.data('lessonid');

		quiz.random = (container.data('random') !== undefined) ? container.data('random') : true;
        //console.log('loadData: random = ' + quiz.random);

		i++;
    });

	statsMax = max;
}

function loadOrder()
{
    //console.log(">>> loadOrder");

	//
	// load random map in a work array
	//
	var order = [];
	for (var i = 0; i < max; i++)
		order[i] = i;

    if (quiz.random)
        order = shuffle(order); // mix it up

	//
	// now copy it to the real place
	//
	for (var i = 0; i < max; i++)
		quiz.qna[i].order = order[i];

    if (false)
    {
        var s = "";
        for (var i = 0; i < max; i++)
            s += quiz.qna[i].order + ",";

        console.log('loadOrder: random = ' + quiz.random);
        console.log(s);
    }

	// now apply the count since all questions have been randomized
    if (quizCount > 0 && quizCount < max)
    {
        max = quizCount;
    	statsMax = max;
    }
}

function first()
{
	curr = 0;
	loadQuestion();
}

function last()
{
	curr = max - 1;
	loadQuestion();
}

function next()
{
	curr++;
	if (curr >= max)
	{
		curr = 0;
		nbr = 0;
	}

	loadQuestion();
}

function prev()
{
	curr--;
	if (curr < 0)
		curr = max - 1;

	loadQuestion();
}

function startQuizWithStartPage() // original: replaced below
{
	setButtonStates(RUNSTATE_START);
	quiz.setControlStates();
	loadData();
	$("#checkbox-type-answers").prop('checked', startWithTypeAnswers());
	quiz.showPanel();
}

function startQuiz()
{
	loadData();
    quiz.runState = RUNSTATE_ASKING; // Skip the start panel
    quiz.start();
}

function stopQuiz()
{
	$("#panelEndofquizFinished").hide();
	$("#panelEndofquizStopped").show();
	quiz.runState = RUNSTATE_ENDOFQUIZ;
	quiz.showPanel();
}

function resetQuiz()
{
	//console.log('>>> RESET QUIZ');

	clear();

	for (var i = 0; i < max; i++)
		quiz.qna[i].correct = false;

	curr = 0;
	right = 0;
	wrong = 0;
	round = 1;
	nbr = 0;
	statsMax = max;

	loadOrder();

	resetEndPanels();
}

function clear2()
{
	clear();
}

function clear()
{
	$("#promptQuestion").val('');
	$("#promptQuestion").text('');
	$("#prompt").val('');
	$("#prompt").text('');

	$("#attemptInput").val('');
	$("#attemptInput").text('');

	$("#answer-show").val('');
	$("#answer-show").text('');

	$("#answer-show-div").text('');
}

function getAnswer(index = null)
{
	var rc = null;
	index = (index == null) ? quiz.qna[curr].order : index;

	if (quiz.flipped())
	{
		rc = quiz.qna[index].q;
	}
	else
	{
		if (quiz.useDefinition())
			rc = quiz.qna[index].definition;
		else
			rc = quiz.qna[index].a;
	}

	return rc;
}

function getQuestion(index = null)
{
	var rc = null;
	index = (index == null) ? quiz.qna[curr].order : index;

	if (quiz.flipped())
	{
		if (quiz.useDefinition())
			rc = quiz.qna[index].definition;
		else
			rc = quiz.qna[index].a;
	}
	else
	{
		rc = quiz.qna[index].q;
	}

	return rc;

}

function loadQuestion()
{
	showQuestion();
	nbr++;
	updateScore();

	quiz.setAlertPrompt(quiz.promptQuestion, COLOR_QUESTION_PROMPT);
}

function reloadQuestion()
{
	showQuestion();

	// one of these triggered this call so save the state
	var checked = $('#checkbox-flip').prop('checked') ? 'true' : '';
	localStorage.setItem('checkbox-flip', checked);

	checked = $('#checkbox-use-definition').prop('checked') ? 'true' : '';
	localStorage.setItem('checkbox-use-definition', checked);
}

function updateRandom(reloadOrder = true)
{
    // set it from the checkbox
	var random = $('#checkbox-random').prop('checked');

	// save it for a page reload
	localStorage.setItem('checkbox-random', random ? 'true' : '');

    //console.log('updateRandom: random = ' + random);
    if (quiz.random != random)
    {
        // set it on the quiz
        quiz.random = random;

        // update the question order to randomize
        if (reloadOrder)
            loadOrder();
    }
}

function toStringBoolArray(a)
{
	var s = '';

	for (var i = 0; i < a.length; i++)
	{
		s += (a[i] ? "1" : "0");
	}

	return s;
}

function onKeypress(e)
{
	if (e.keyCode == 13)
	{
		e.stopImmediatePropagation();
		e.preventDefault();
		checkAnswer(CHECKANSWER_NORMAL);
		return false;
	}
	else
	{
		$("#answer-show").val('');
		//$("#answer-show").text('');
	}
}

function cleanUpSpecialChars(str)
{
	var start = str;

    str = str.replace(/[����]/g,"e");
    str = str.replace(/[������]/g,"A");
    str = str.replace(/[������]/g,"a");
    str = str.replace(/[����]/g,"E");
	//str = str.replace(/[^a-z0-9]/gi,''); // final clean up
	//alert(str);

	//if (str == 'Noumea' || str == 'Noum�a' || start != str)
	//alert('start: ' + start + ", str: " + str);

    return str;
}

function checkAnswerFromButtonClick(event)
{
	checkAnswerFromButton(event.target.id);
}

function checkAnswerFromButton(id, showOnly = false)
{
	var correctButtonClicked = false;
	id = '#' + id;

	if (quiz.runState == RUNSTATE_ASKING)
	{
		if ($(id).hasClass("btn-right"))
		{
			// if RIGHT answer chosen
			correctButtonClicked = true;
		}
		else
		{
			// if WRONG answer chosen, mark so we can show it as red
			$(id).addClass( "btn-chosen" ); // set a class on the chosen button so we don't have to pass the id all the way through
		}

		//alert(answer);
		var timerSeconds = 2;
		if (!checkAnswer(CHECKANSWER_FROMBUTTON, correctButtonClicked, showOnly))
		    timerSeconds *= 1; // optional: add extra time for wrong answer, make more than 1

		// load next question on a timer
		nextAttemptTimer = setTimeout(nextAttempt, timerSeconds * 1000 /* make it milliseconds */);
	}
	else if (quiz.runState == RUNSTATE_CHECKING)
	{
		nextAttempt();
	}
}

function checkAnswer(checkOptions, correctButtonClicked = false, showOnly = false)
{
    //console.log('checkAnswer()');

	setButtonStates(RUNSTATE_CHECKING);
	$("#question-prompt").hide();

	var answerRaw = getAnswer();
	var answer = cleanUpSpecialChars(answerRaw);
	var attempt = $("#attemptInput").val();
	var rightAnswer = false;
	var result = '';
	var answerColor = 'black';

	if (checkOptions == CHECKANSWER_KNOW)
	{
		answerColor = "#4993FD";
		result = quiz.quizTextCorrectAnswer;
		quiz.qna[quiz.qna[curr].order].correct = true;
		$("#button-next-attempt").focus();
		quiz.showOverrideButton(true, quiz.quizTextOverrideWrong);
		quiz.lastScore = SCORE_CORRECT;
		$("#question-right").show();

		right++;
		rightAnswer = true;
	}
	else if (checkOptions == CHECKANSWER_DONTKNOW)
	{
		result = quiz.quizTextWrongAnswer;
		answerColor = 'red';
		$("#button-next-attempt").focus();
		quiz.showOverrideButton(true, quiz.quizTextOverrideCorrect);
		quiz.lastScore = SCORE_WRONG;
		$("#question-wrong").show();

		wrong++;
	}
	else
	{
		var correctAnswer = false;
		if (checkOptions == CHECKANSWER_FROMBUTTON)
		{
			correctAnswer = correctButtonClicked;
		}
		else
		{
			// typing the answers so check the entry
			cleanAnswer = cleanQna(jQuery('<span>').html(answer).text());
			cleanAttempt = cleanQna(jQuery('<span>').html(attempt).text());
			if (cleanAnswer != cleanAttempt)
			{
				cleanAnswer = accentFold(cleanAnswer);
				cleanAttempt = accentFold(cleanAttempt);
			}

			correctAnswer = ((answer != null && attempt != null) && cleanAnswer == cleanAttempt);
		}

		if (correctAnswer)
		{
			if (showOnly)
			{
				result = quiz.quizTextMarkedWrong;
				answerColor = 'purple';
				quiz.qna[quiz.qna[curr].order].correct = false;
			}
			else
			{
				result = quiz.quizTextCorrectAnswer;
				answerColor = 'green';
				quiz.qna[quiz.qna[curr].order].correct = true;
			}

			$("#button-next-attempt").focus();
			quiz.showOverrideButton(false, quiz.quizTextOverrideWrong);
			quiz.lastScore = SCORE_WRONG;
			$("#question-right").show();

			if (showOnly)
			{
				wrong++;
			}
			else
			{
				right++;
				rightAnswer = true;

				// mark the question since it was answered correctly
				//touch(quiz.qna[quiz.qna[curr].order]);
			}
		}
		else
		{
			result = quiz.quizTextWrongAnswer;
			answerColor = 'red';
			$("#button-next-attempt").focus();
			quiz.showOverrideButton(true, quiz.quizTextOverrideCorrect);
			quiz.lastScore = SCORE_WRONG;
			$("#question-wrong").show();
			wrong++;
		}
	}

	quiz.setAlertPrompt(result, answerColor, /* bold = */ true);

	var answerMsg = answer;
	if (answer != answerRaw)
		answerMsg += " (" + answerRaw + ")";

	//alert(answer);

	if (quiz.isMc)
	{
		// the answer is shown in the button
		$("#answer-show-div").hide();
	}
	else
	{
		$("#answer-show-div").show();
		$("#answer-show-div").html(answerMsg);
		$("#answer-show-div").css('color', answerColor);
	}

	updateScore();

	return rightAnswer;
}

function cleanQna(str)
{
	str = str.toLowerCase().trim();
	str = str.replace(/\.|\,/gi, ""); // remove all ',' and '.'

	return str;
}

function updateScore()
{
	var total = right + wrong;
	var percent = total > 0 ? (right / total) * 100 : 0;
	percent = percent.toFixed(2).replace(/\.?0*$/,'');

	$("#statsCount").html("<span class='quizStats'>" + quiz.quizTextQuestion + " " + nbr + " " + quiz.quizTextOf + " " + statsMax + "</span>");
	$("#statsScore").html("<span class='quizStats'>" + quiz.quizTextCorrect + ": " + right + "/" + total + " (" + percent + "%)</span>");
	$("#statsDebug").html("<span class='quizStats'>"
		+ "round=" + round
		+ ", right=" + right
		+ ", wrong=" + wrong
		+ ", curr=" + curr
		+ ", order=" + quiz.qna[curr].order
		+ ", nbr=" + nbr
		+ ", max=" + max
		+ ", statsMax=" + statsMax
		+ "<br/>"
		//+ "order=" + quiz.order.toString()
		//+ ", correct=" + toStringBoolArray(quiz.correct)
		+ "<br/>"
		+ "<span style='font-size: 55%; '>"
		//+ "q=" + quiz.questions.toString()
		+ "</span>"
		+ "</span>");
}

function override()
{
	quiz.showOverrideButton(false, null);

	var answer = getAnswer();
	var result = "";
	var color = "black";

	if (quiz.lastScore == SCORE_NOTSET)
	{
		// no action
		alert('bad logic: no last score');
	}
	else if (quiz.lastScore == SCORE_WRONG)
	{
		//
		// it was wrong, make it right
		//
		quiz.qna[quiz.qna[curr].order].correct = true;
		$("#question-right").show();
		$("#question-wrong").hide();
		$("#question-prompt").hide();
		result = "Correct: ";
		color = "darkBlue";
		right++;
		wrong--;
	}
	else if (quiz.lastScore == SCORE_CORRECT)
	{
		//
		// it was right, make it wrong
		//
		$("#question-right").hide();
		$("#question-wrong").show();
		$("#question-prompt").hide();
		quiz.qna[quiz.qna[curr].order].correct = false;
		result = "Wrong: ";
		color = "red";

		right--;
		wrong++;
	}

	quiz.setAlertPrompt(quiz.quizTextScoreChanged, color);

	answer = result + answer;
	$("#answer-show").html(answer);
	$("#answer-show").val(answer);
	$("#answer-show").css("color", color);
	$("#answer-show-div").html(answer);
	$("#answer-show-div").val(answer);
	$("#answer-show-div").css("color", color);
	updateScore();
	$("#button-next-attempt").focus();
}

function startWithTypeAnswers()
{
	if (isMobile.any())
		return false;

	if (quiz.isMc)
		return false;

	return true;
}

var accentMap = {
  'á':'a',
  'é':'e',
  'í':'i',
  'ó':'o',
  'ú':'u',
  'ü':'u',
  'ñ':'n',
  'Á':'A',
  'É':'E',
  'Í':'I',
  'Ú':'U',
  'Ü':'U',
  'Ñ':'N'
};

function accentFold (s)
{
	if (!s) { return ''; }
	var ret = '';
	for (var i = 0; i < s.length; i++) {
		ret += accentMap[s.charAt(i)] || s.charAt(i);
	}

	return ret;
}

function touch(q, correct = false)
{
    // if it's a word, update it's last display time
    if (quiz.touchPath !== null && quiz.touchPath.length > 0) // if touchPath set
    {
        var path = quiz.touchPath + '?definition_id=' + q.id + '&qna_attempts=1' + '&qna_correct=' + (correct ? 1 : 0);
        ajaxexec(path); // touch it and update stats

        //console.log('qnabase::touch() ajax path: ' + path);
    }
    else
    {
    	//console.log('qnabase::touch() path not set');
    }
}

function addHistory()
{
    addHistoryRecord(quiz.historyPath, quiz.programName, quiz.programId, quiz.programType, quiz.programSubType, quiz.programAction, quiz.sessionName, quiz.sessionId, max, quiz.historyRoute);
}

function flip()
{
	quiz.flip();
}

function setButtonStates(state)
{
	quiz.runState = state;

	if (quiz.isMc)
	{
		$(".hide-for-mc").hide();
	}

	if (state == RUNSTATE_START)
	{
		//
		// only show the start button
		//
		quiz.showOverrideButton(false, null);
		$("#button-check-answer").hide();
		$("#button-next-attempt").hide();
		$("#button-know").hide();
		$("#button-dont-know").hide();

		$("#button-stop").hide();

		$("#question-right").hide();
		$("#question-wrong").hide();
		$("#question-prompt").hide();

		$("#attemptInput").hide();
	}
	else if (state == RUNSTATE_ASKING)
	{
		//
		// asking the question
		//

		if (quiz.isMc)
		{
			$("#button-dont-know").hide();
			$("#button-check-answer").hide();
			$("#button-know").hide();
		}
		else
		{
			$("#button-check-answer").hide();
			$("#button-dont-know").show();
			$("#button-know").show();
			$("#button-know").focus();
		}

		quiz.showOverrideButton(false, null);
		$("#button-next-attempt").hide();
		$("#button-stop").show();

		$("#question-right").hide();
		$("#question-wrong").hide();
		$("#question-prompt").show();
	}
	else if (state == RUNSTATE_CHECKING)
	{
		$("#question-prompt").hide();

		//
		// checking the answer
		//
		$("#button-check-answer").hide();
		$("#button-know").hide();
		$("#button-dont-know").hide();

		quiz.showOverrideButton(true, null);
		$("#button-stop").show();

		if (quiz.isMc)
		{
			// change the button colors to show the answer
			$(".btn-right").css('background-color','#5CB85C');
			$(".btn-right").css('border-color','#5CB85C');

			// wrong button option
			$(".btn-wrong").css('background-color','LightGray');
			$(".btn-wrong").css('border-color','LightGray');

			// answer chosen but wrong
			$(".btn-chosen").css('background-color','red');
			$(".btn-chosen").css('border-color','black');

			// [I don't know] button clicked, show answer in yellow
			$(".btn-right-show").css('color', 'purple');
			$(".btn-right-show").css('background-color', '#ffc107');
			$(".btn-right-show").css('border-color', '#ffed4a');

			// check if the chosen button is invisible
			//if ($(".btn-chosen").is(":hidden"))
			//{
			//	$(".btn-chosen").css('color','red');
			//}

			$("#button-next-attempt").show();
		}
		else
		{
			$("#button-next-attempt").show();
		}
	}
	else
	{
		alert("setButtonStates - bad value");
	}
}

function continueQuiz()
{
	// if end of round but not end of quiz, keep asking
	if (quiz.runState == RUNSTATE_ENDOFROUND)
		quiz.runState = RUNSTATE_ASKING;

	quiz.showPanel();
}
