//-----------------------------------------------------------------------------
// THE TIMED SLIDES JS APPLICATION
//-----------------------------------------------------------------------------

//
// Constants
//
const RUNSTATE_START        = 1;
const RUNSTATE_COUNTDOWN    = 2;
const RUNSTATE_RUN          = 3;
const RUNSTATE_BETWEEN      = 4;
const RUNSTATE_END          = 5;

//
// numbers
//
var curr = 0;   // current slide
var nbr = 0;
var max = 0;    // number of slides

var _debug = true;
var _mute = false;

function debug(text)
{
    if (_debug)
        console.log(text);
}

$( document ).ready(function() {
	loadData();
	deck.start();
});

//
// slide class
//
function deck() {

	this.slides = [];   // slides
    this.bgs = [];      // slide background image

	// options
	this.runState = RUNSTATE_START;

	//new:
	//this.quizTextRound = 'not set';
	//this.quizTextCorrect = 'not set';
	this.lessonId = 'not set';

	this.getId = function(index) {
		return this.slides[this.slides[index].order].id;
	}

	this.slide = function(index) {
		return this.slides[this.slides[index]];
	}

	this.start = function() {
        this.setStates(RUNSTATE_START);
	}

    // this shows the beginning count down and then starts the first slide
	this.run = function() {
		reset();
		this.setStates(RUNSTATE_COUNTDOWN);
	    deck.showSlide();
	    deck.setBackgroundImage(); // do this here so bg image will be preloaded with no delay
	    _countdownAudioTotalSeconds = 3;
        startTimer(deck.slides[curr].countdown, this.runSlide);
	}

    // this shows the current slide
	this.runSlide = function() {

        debug("run slide: " + deck.slides[curr].title);

        clearTimer();
        if (curr < max)
        {
            loadSlide();
            var seconds = deck.slides[curr].seconds;

    	    _countdownAudioTotalSeconds = 10;
    	    _tensAudio = true;
            if (curr < (max - 1)) // not last one
                startTimer(seconds, deck.runBetween);
            else    // last one
                startTimer(seconds, end);
        }
        else
        {
    		stop();
        }
	}

    // this shows the between slide
	this.runBetween = function() {
        clearTimer();
	    deck.setStates(RUNSTATE_BETWEEN);
	    betweenSeconds = deck.slides[curr].between;
        curr++; // do this here because we need the between seconds from the previous record
	    deck.showSlide(); // show the upcoming slide during the break
	    deck.setBackgroundImage(); // do this here so bg image will be preloaded with no delay
	    _countdownAudioTotalSeconds = 3;
        startTimer(betweenSeconds, deck.runSlide);
	}

	this.skipSlide = function() {

		switch(this.runState)
		{
			case RUNSTATE_COUNTDOWN:
			    this.runSlide();
				break;

			case RUNSTATE_BETWEEN:
			    this.runSlide();
				break;

			case RUNSTATE_RUN:

                if (curr < (max - 1)) // not last one
    			    this.runBetween();
                else    // last one
                    end();
				break;

			default:
			    // for everything else reload the page
			    reload();
				break;
		}

	}

	this.showPanel = function(id) {

        // hide all
		$(".slide-panel").hide();

		// show the current panel
		$(id).show();

	}

	this.setFocus = function() {
		//todo: only done for start slide
		//if (this.isTypeAnswers())
		//	$("#attemptInput").focus();
	}

	this.setStates = function(state) {

        debug("setting state to " + state);

		this.runState = state;

        var id = null;
		switch(state)
		{
			case RUNSTATE_START:
			    id = "#panel-start";
				break;

			case RUNSTATE_COUNTDOWN:
			    id = "#panel-countdown";
				break;

			case RUNSTATE_RUN:
			    id = "#panel-run";
				break;

			case RUNSTATE_BETWEEN:
			    id = "#panel-between";
				break;

			case RUNSTATE_END:
			    id = "#panel-end";
				break;

			default:
				$("#panel-start").show();
				this.setFocus();
				break;
		}

		this.showPanel(id);
	}

	this.showSlide = function() {
	    var slide = deck.slides[curr];
        $(".slideCount").text(slide.number + " of " + deck.slides.length);
        $(".slideTitle").text(slide.number + ". " + slide.title);
        $(".slideSeconds").text(slide.seconds + " seconds");
        $(".slideDescription").text(deck.slides[curr].description);
        $(".sliderPhoto").attr("src", "/img/plancha/" + deck.slides[curr].photo)
        //alert(deck.slides[curr].photo);
        //$(".slidePhoto").text();
	}

	this.setBackgroundImage = function() {
	    var bg = getRandomBackground();
        $("#bg").css("background-image", bg);
	}

	this.setAlertPrompt = function(text, color, bold = false) {
		//$("#alertPrompt").html(text);
		//$("#alertPrompt").css('color', color);
		//$("#alertPrompt").css('font-weight', bold ? 'bold' : 'normal');
	}
}

var deck = new deck();

function getRandomBackground()
{
    // get random background image
    var ix = Math.floor(Math.random() * deck.bgs.length);
    var bg = deck.bgs[ix];
    var i = 0;
    var maxLoops = deck.bgs.length;
    while (bg['shown'] && i < maxLoops)
    {
        ix++;
        if (ix >= deck.bgs.length)
            ix = 0;

        bg = deck.bgs[ix];
        i++; // make sure it's not infinite
        debug('looping...');
    }

    if (i >= maxLoops)
        debug('ran out of unused bg images')

    debug('showing: ' + bg['filename']);
    deck.bgs[ix]['shown'] = true;

    bg = 'url(/img/backgrounds/' + deck.bgAlbum + '/' + bg['filename'] + ')';

    return bg;
}

function getBackground()
{
    // get unused bg image
    var ix = 0;
    var bg = deck.bgs[ix];
    var i = 0;
    var maxLoops = deck.bgs.length;
    while (bg['shown'] && i < maxLoops)
    {
        ix++;
        if (ix >= deck.bgs.length)
            ix = 0;

        bg = deck.bgs[ix];
        i++; // make sure it's not infinite
        debug('looping...');
    }

    if (i >= maxLoops)
        debug('ran out of unused bg images')

    debug('showing: ' + bg['filename']);
    deck.bgs[ix]['shown'] = true;

    bg = 'url(/img/backgrounds/' + deck.bgAlbum + '/' + bg['filename'] + ')';

    return bg;
}

function loadData()
{
	//
	// load slides arrays from the html tag 'data-' attributes, for example: data-question, data-answer, data-prompt
	//
	var i = 0;

	$('.data-slides').each(function() {
        var container = $(this);
        var service = container.data('title');

		var title = container.data('title');
		var number = parseInt(container.data('number'));
		var description = container.data('description');
		var id = container.data('id');
        var seconds = parseInt(container.data('seconds'));
        var between = parseInt(container.data('between'));
        var countdown = parseInt(container.data('countdown'));
        var photo = container.data('photo');
        var reps = 0;

		// add the record
		deck.slides[i] = {
		    title:title.toString(),
		    number:number,
		    description:description.toString(),
		    id:id.toString(),
		    order:0,
		    seconds:seconds,
		    between:between,
		    countdown:countdown,
		    photo:photo,
		    reps:reps,
		    done:false
		};

		//alert(deck.slides[i].between);
		//if (i == 0) alert(deck.slides[i].q);

		i++;
    });

	//
	// load misc variables
	//
	$('.data-misc').each(function() {
        var container = $(this);

		max = container.data('max');

		// new settings
		deck.quizTextDone = container.data('quiztext-done');
		deck.lessonId = container.data('lessonid');
		deck.touchPath = container.data('touchpath');
		deck.bgAlbum = container.data('bgalbum');
    });

	//
	// load bg images
	//
	i = 0;
	$('.data-bgs').each(function() {

        var container = $(this);

		var filename = container.data('filename');

		// add the record
		deck.bgs[i] = {
		    filename:filename,
		    shown:false
		};

		i++;
    });
	//alert("max=" + max);
}

function first()
{
	curr = 0;
	loadSlide();
}

function last()
{
	curr = max - 1;
	loadSlide();
}

function next()
{
	curr++;
	if (curr >= max)
	{
		curr = 0;
		nbr = 0;
	}

	loadSlide();
}

var _countdownTimer = null;
var _deckTimer = null;
var _timerIntervalCounter = 0;
var _countdownAudioTotalSeconds = 0;
var _tensAudio = false;
var _timersPaused = false;

function startTimer(seconds, func)
{
	clearTimeout(_deckTimer);

	// set the timer for the next panel
    _deckTimer = setTimeout(func, seconds * 1000);

    // start the second countdown timer
    startInterval(seconds);

    showSeconds(seconds);
}

function clearTimer()
{
    clearInterval(_countdownTimer);
    _timerIntervalCounter = 0;
    showSeconds();
    _countdownAudioTotalSeconds = 0;
    _tensAudio = false;
}

// skip the current countdown, slide, or between break
function skip()
{
    if (true)
        deck.skipSlide();
    else
        switchBackgroundPhoto(); // tester to cycle through all background images
}

function switchBackgroundPhoto()
{
    clearTimer();

    // loop thru the bg images
    var bg = getBackground();
    $("#bg").css("background-image", bg);
    $("#bg-photo-name").text(bg);
}

// reload the page
function reload()
{
    location.reload();
}

function pause()
{
    debug("pause hit, _timersPaused = " + _timersPaused.toString());

    if (_timersPaused)
    {
        //
        // timers paused, restart them
        //
        resume();
    }
    else
    {
        //
        // timers running, pause them
        //
        clearInterval(_countdownTimer);
        _countdownTimer = null;

        clearTimeout(_deckTimer);
        _deckTimer = null;
    }

    _timersPaused = !_timersPaused;

    if (_timersPaused)
    {
        $("#button-pause").removeClass("glyphicon-pause");
        $("#button-pause").addClass("glyphicon-play");
    }
    else
    {
        $("#button-pause").removeClass("glyphicon-play");
        $("#button-pause").addClass("glyphicon-pause");
    }
}

function resume()
{
    debug("resume: time left on countdown timer = " + _timerIntervalCounter);

    if (_timerIntervalCounter <= 0)
    {
        debug("nothing to resume");
        return; // nothing to do
    }

    // reset to the remaining time on the counter
    seconds = _timerIntervalCounter;

    // restart the deck timer according to the current state
    switch(deck.runState)
    {
        case RUNSTATE_COUNTDOWN:
            debug("resuming countdown");
            startTimer(seconds, deck.runSlide);
            break;

        case RUNSTATE_BETWEEN:
            debug("resuming between break");
            startTimer(seconds, deck.runSlide);
            break;

        case RUNSTATE_RUN:
            if (curr < (max - 1)) // not last one
            {
                debug("resuming slide");
                startTimer(seconds, deck.runBetween);
            }
            else    // last one
            {
                debug("resuming last slide");
                startTimer(seconds, end);
            }
            break;

        default:
            // nothing to do
            debug("nothing to resume");
            break;
    }

}

function startInterval(seconds)
{
    _timerIntervalCounter = seconds;
    _countdownTimer = setInterval(updateTimer, 1000);
    showSeconds(_timerIntervalCounter);
}

function updateTimer()
{
    // if speaking on the 10s like "50 seconds remaining" AND it's on a 10 multiple AND the 10 multiple is more than 10
    if (_tensAudio && ((_timerIntervalCounter-1) % 10) == 0 && (_timerIntervalCounter-1) > 10)
    {
        playAudio(_timerIntervalCounter-1);
    }

    if (_countdownAudioTotalSeconds > 0 && _timerIntervalCounter <= (_countdownAudioTotalSeconds + 1))
        playAudio(_timerIntervalCounter - 1);

    _timerIntervalCounter--;
    showSeconds(_timerIntervalCounter);

    if (_timerIntervalCounter <= 0)
       clearTimer();
}

function playAudio(seconds)
{
    if (seconds <= 10)
    {
        text = seconds.toString();
    }
    else
    {
        text = seconds.toString() + " seconds remaining";
    }

    tts(text);
}

function mute()
{
    _mute = !_mute;

    if (_mute)
    {
        $("#button-mute").removeClass("glyphicon-volume-up");
        $("#button-mute").addClass("glyphicon-volume-off");
    }
    else
    {
        $("#button-mute").removeClass("glyphicon-volume-off");
        $("#button-mute").addClass("glyphicon-volume-up");
    }

    debug("mute set to " + _mute.toString());
}

function playAudioFile(file)
{
    if (!_mute)
    {
        var a = document.getElementById("audio");
        var src = "/audio/" + file;
        $("#audio").attr("src", src)
        a.play();
    }
}

function tts(text)
{
    if (!_mute)
    {
        var utter = new SpeechSynthesisUtterance();

        //var myLang = utter.lang;
        utter.lang = 'en-US';
        utter.text = text;
        window.speechSynthesis.speak(utter);
    }
}

function setDebug(text = null)
{
    $("#debug").text(text);
}

function showSeconds(text = null)
{
    $(".showSeconds").text(text);
}

function run()
{
    deck.run();
}

function stop()
{
	deck.setStates(RUNSTATE_END);
    clearTimer();
}

function end()
{
    stop();
    playAudioFile("small-crowd-applause.mp3");
}

function reset()
{
	clear();

    // clear slides
	deck.slides.forEach(function(slide, index){
	    slide.done = false;
	});

    // clear bg images
    deck.bgs.forEach(function(bg, index){
        bg.shown = false;
    });

	curr = 0;
	nbr = 0;

	//$("#stats").hide();
	//$("#panelEndofquizFinished").show();
	//$("#panelEndofquizStopped").hide();
}

function clear()
{
	//$("#promptQuestion").val('');
	//$("#promptQuestion").text('');
	//$("#prompt").val('');
	//$("#prompt").text('');
}

function loadSlide()
{
	deck.setStates(RUNSTATE_RUN);
	deck.showSlide();
	updateStatus();
}

function onKeypress(e)
{
	if (e.keyCode == 13) // enter key
	{
		e.stopImmediatePropagation();
		e.preventDefault();
		return false;
	}
	else
	{
		//$("#answer-show").val('');
	}
}

function updateStatus()
{
/*
	var total = right + wrong;
	var percent = total > 0 ? (right / total) * 100 : 0;
	percent = percent.toFixed(2).replace(/\.?0*$/,'');

	$("#statsCount").html("<span class='quizStats'>" + deck.quizTextQuestion + ": " + nbr + "/" + statsMax + "</span>");
	$("#statsScore").html("<span class='quizStats'>" + deck.quizTextdone + ": " + right + "/" + total + " (" + percent + "%)</span>");
	$("#statsDebug").html("<span class='quizStats'>"
		+ "round=" + round
		+ ", right=" + right
		+ ", wrong=" + wrong
		+ ", curr=" + curr
		+ ", order=" + deck.slides[curr].title
		+ ", nbr=" + nbr
		+ ", max=" + max
		+ ", statsMax=" + statsMax
		+ "<br/>"
		+ "<br/>"
		+ "<span style='font-size: 55%; '>"
		+ "</span>"
		+ "</span>");
*/
}

function touch(q)
{
    // if it's a word, update it's last display time
    if (deck.touchPath.length > 0) // if touchPath set
    {
        var path = '/' + deck.touchPath + '/' + q.id;
        ajaxexec(path);

        //alert('id: ' + q.id + ', word: ' + q.a);
    }
}
