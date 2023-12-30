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
var max = 0;    // number of slides
var _readContinuous = false; // read and loop
var _readPage = false;

var _debug = false;
var _mute = false;
var _paused = false;
var _lastCharIndex = 0;
var _cancelled = false;
var _readFontSize = 18;
var _minFontSize = 14;
var _maxFontSize = 99;
var _hotWords = [];
var _bottomPanelHeight; // height of bottom button panel
var _incLine = 0; // helper to get to a starting line
// track read time
var _startTime = null;
var _useKeyboard = true;

$(document).ready(function() {

    console.log('reader.js ready');

	var fontSize = localStorage['readFontSize'];
	if (!fontSize)
	{
		localStorage['readFontSize'] = _readFontSize;
	}
	else
	{
		_readFontSize = parseInt(fontSize, 10);
		if (_readFontSize > _maxFontSize)
			_readFontSize = _maxFontsize;
	}
	setFontSize();

	loadData();
	getReadLocation();
	deck.start();

	$("#pause").hide();
	$("#resume").show();
	//ajaxexec('/entries/get-definitions-user/' + parseInt(deck.contentId, 10) + '', '#defs');

	_bottomPanelHeight = $("#bottom-panel").outerHeight(); // needed for scrolling
	//console.log("bottom panel height: " + _bottomPanelHeight);

    //console.log('page load ready');
    if (typeof loadRecorder === "function") // if function is defined call it
	    loadRecorder();
	else
	    console.log('loadRecorder not found');

    //
    // get the last pause seconds setting and use it
    //
    var pauseSeconds = localStorage[deck.pauseSecondsId];
	pauseSeconds = (pauseSeconds) ? parseInt(pauseSeconds) : 0;
	//console.log('pause seconds: ' + pauseSeconds);
    $(deck.pauseSecondsId).html(pauseSeconds);

    //
    // get the last random setting and use it
    //
    var randomOrder = localStorage['#random_order'];
	randomOrder = (randomOrder) ? randomOrder : "";
	//console.log('pause seconds: ' + pauseSeconds);
    $('#random_order').val(randomOrder);

});

$(window).on('unload', function() {
	window.speechSynthesis.cancel();
});

$(document).keyup(function(event) {

    if (_useKeyboard)
    {
        if(event.keyCode == 32)		// spacebar
        {
            togglePause();
        }
        else if(event.keyCode == 37) // left arrow
        {
            prev();
        }
        else if(event.keyCode == 39) // right arrow
        {
            next();
            touchViews();
        }
    }

});

//
// slide class
//
function deck() {

	this.slides = [];   // slides
    this.slideOrder = null; // slide order
	this.speech = null;
	this.language = "not set";
	this.languageLong = "";
	this.isAdmin = false;
	this.userId = 0;
    this.title = 'not set';
    this.locale = 'not set';

    // add History
    this.programName = 'not set';
    this.programId = 0;
    this.programType = 0;
    this.programSubType = 0;
    this.sessionName = 'not set';
    this.sessionId = 0;
    this.historyPath = 'not set';
    this.historyRoute = 'not set';

	// options
	this.runState = RUNSTATE_START;

	this.contentType 	 = 'contentTypeNotSet';	// type of the content being read
	this.contentId 		 = 'contentIdNotSet';	// id of the content being read
	this.readLocationTag = 'readLocation';		// readLocation session id tag
	this.readLocationOtherDevice = 0;			// read location from another device for logged in user
    this.randomOrder = false;                   // read slides in random order

	// labels
	this.labelStart = 'Start not set';
	this.labelStartBeginning = 'Start beginning not set';
	this.labelContinue = 'Continue not set';
    this.labelLocationDifferent = 'Location different not set';
	this.labelLine = 'Line not set';
	this.labelOf = 'Of not set';

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
	this.run = function(fromBeginning = true) {
		if (fromBeginning)
			reset();
		this.setStates(RUNSTATE_COUNTDOWN);
	    deck.showSlide();
		this.runSlide();
	}

    // this shows the current slide
	this.runSlide = function() {

        clearTimeout(_pauseTimerId);

		//debug("read next: " + curr, _debug);

        if (curr < max)
        {
			//console.log('runSlide: max: ' + max + ", current: " + curr);
            loadSlide();
			deck.readSlide();
        }
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

        //debug("setting state to " + state, _debug);

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
        $("#slideCount").text((curr+1) + " " + deck.labelOf + " " + max);
        $(".slideDescription").text(deck.getText());
		$('#selected-word').text('');
		$('#selected-word-definition').text('');

		$('#slideTranslaiton').text('');
        $("#slideTranslation").hide();

		if ($('#tab1').is(':visible'))
			window.scroll(0, 0); // scroll to top

	}

	this.isFlipped = function() {
		return $('#checkbox-flip').prop('checked');
	}

    this.getText = function (mainText = true) {

	    var slide = getCurrentSlide();

        if (mainText)
        {
            return this.isFlipped() ? slide.translation : slide.description;
        }
        else // get translation
        {
            return this.isFlipped() ? slide.description : slide.translation;
        }
    }

	this.showTranslation = function() {
        $("#slideTranslation").text(deck.getText(/* main text = */ false));
        $("#slideTranslation").toggle();
	}

	this.readSlideResume = function() {
		read(deck.getText(), _lastCharIndex);
	}

	this.readSlide = function() {
		read(deck.getText(), 0);
	}

	this.setAlertPrompt = function(text, color, bold = false) {
		//$("#alertPrompt").html(text);
		//$("#alertPrompt").css('color', color);
		//$("#alertPrompt").css('font-weight', bold ? 'bold' : 'normal');
	}
}

var deck = new deck();

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

		var number = 1;
		var description = container.data('description');
		var translation = container.data('translation');
		translation = (typeof translation !== 'undefined') ? translation : '';

		var id = container.data('id');
        var seconds = parseInt(container.data('seconds'));
        var between = parseInt(container.data('between'));
        var countdown = parseInt(container.data('countdown'));
        var reps = 0;

		// add the record
		deck.slides[i] = {
		    title:title.toString(),
		    number:number,
		    description:description.toString(),
		    translation:translation.toString(),
		    id:id.toString(),
		    order:0,
		    seconds:seconds,
		    between:between,
		    countdown:countdown,
		    reps:reps,
		    done:false
		};

		//alert(deck.slides[i].between);
		//if (i == 0) alert(deck.slides[i].q);

		i++;
    });

    deck.slideOrder = loadOrder();

	//
	// load misc variables
	//
	$('.data-misc').each(function() {
        var container = $(this);

		max = container.data('max');

        // for History record
		deck.title = container.data('title');
		deck.programName = container.data('programname');
		deck.programId = container.data('programid');
		deck.programType = container.data('programtype');
		deck.programSubType = container.data('programsubtype');
		deck.programAction = container.data('programaction');
		deck.sessionName = container.data('sessionname');
		deck.sessionId = container.data('sessionid');
		deck.historyPath = container.data('historypath');
		deck.historyRoute = container.data('historyroute');
        deck.showParameters();

    	// new settings
		deck.quizTextDone = container.data('quiztext-done');
		deck.touchPath = container.data('touchpath');
		deck.language = container.data('language');             // this is the language that the web site is in
		deck.locale = container.data('locale');                 // needed to create links
		deck.languageLong = container.data('language-long');	// long version like: eng-BGR
		//console.log('languages: ' + deck.language + ", " + deck.languageLong);
		deck.isAdmin = container.data('isadmin') == '1';
		deck.userId = parseInt(container.data('userid'), 10);

		// use these to create a unique session id tag, looks like: 'readLocationEntry23'
		deck.contentType = container.data('contenttype');
		deck.contentId = container.data('contentid');
		deck.readLocationTag += deck.contentType + deck.contentId;

        // to save and reload read 'pause seconds'
		deck.pauseSecondsId = container.data('pausesecondsid');

		// this is the read location from the db
		deck.readLocationOtherDevice = parseInt(container.data('readlocation'), 10);
        //console.log('read location: ' + deck.readLocationOtherDevice);

        // use keyboard
		_useKeyboard = parseInt(container.data('usekeyboard'), 10);

		// random order
		deck.randomOrder = (parseInt(container.data('randomorder')) > 0);
		//console.log('random order: ' + deck.randomOrder);

		// labels
		deck.labelStart = container.data('labelstart');
		deck.labelStartBeginning = container.data('labelstartbeginning');
		deck.labelContinue = container.data('labelcontinue');
		deck.labelLocationDifferent = container.data('labellocationdifferent');
		deck.labelLine = container.data('labelline');
		deck.labelOf = container.data('labelof');
		deck.labelReadingTime = container.data('labelreadingtime');
		//console.log('start: ' + deck.labelStart);

    });

    //console.log('random: ' + $('input[name="random_order"]:checked').val());
}

function loadOrder()
{
	//
	// load random map in a work array
	//
	var order = [];
	for (var i = 0; i < deck.slides.length; i++)
		order[i] = i;

	order = shuffle(order); // mix it up

	return order;
}

function getCurrentId()
{
    var randomOrder = $('input[name="random_order"]:checked').val();
    //console.log('random order: ' + randomOrder);
    var index = (randomOrder == "1") ? deck.slideOrder[curr] : curr;

    return deck.slides[index].id;
}

function getCurrentSlide()
{
    var randomOrder = $('input[name="random_order"]:checked').val();
    //console.log('random order: ' + randomOrder);
    var index = (randomOrder == "1") ? deck.slideOrder[curr] : curr;

    return deck.slides[index];
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

function prev()
{
	pause();
	_cancelled = true;
	_lastCharIndex = 0;

	curr--;
	if (curr < 0)
		curr = max - 1;

	loadSlide();
}

function incLine(e, count)
{
	e.preventDefault();

	_incLine += count + 1;

	// put the line on multiples of 50
	mod = _incLine % 50;
	_incLine -= (mod + 1);

	if (_incLine < 0)
		_incLine = 0;
	else if (_incLine >= max)
		_incLine = 0;

	$('#button-start-reading').text(deck.labelStartBeginning);//"Start reading from the beginning");
	$('#readCurrLine').text(deck.labelLine + " " + (_incLine + 1));
	$('#button-continue-reading').show();
	$('#button-continue-reading').text(deck.labelContinue + " " + (_incLine + 1)); //"Continue reading from line "

	curr = _incLine;
}

function inc(e, id, count)
{
	e.preventDefault();

	var amount = parseInt($(id).text(), 0);
	//console.log('amount: ' + amount);

	amount += parseInt(count);
	if (amount < 0)
	    amount = 0;

	$(id).html(amount);

	localStorage[id] = amount;
}

function next()
{
	pause();
	_cancelled = true;
	_lastCharIndex = 0;

	curr++;
	if (curr >= max)
	{
		curr = 0;
		addHistory();
	}

	loadSlide();
}

// skip the current countdown, slide, or between break
function skip()
{
    if (true)
        deck.skipSlide();
}

// reload the page
function reload()
{
    location.reload();
    releaseWakeLock();
}

function run()
{
    //
    // start reading
    //
	resume();
}

// The wake lock sentinel.
let wakeLock = null;

function hasWakeLock()
{
    if ('wakeLock' in navigator) {
          //console.log('Wake Lock supported');
          return true;
    }
    else {
        console.log('Wake Lock NOT supported');
        return false;
    }
}

// Function that attempts to request a screen wake lock.
async function requestWakeLock()
{
    var msg = 'wake lock off';
    var statusMsg = $('.statusMsg');
    statusMsg.text(msg);

    if (!hasWakeLock())
    {
        statusMsg.text('screen lock not available');
        return; // not available
    }

    if (wakeLock) // already locked
    {
        statusMsg.text('wake lock on');
        return;
    }

    try {
        wakeLock = await navigator.wakeLock.request();

        wakeLock.addEventListener('release', () => {
            //console.log('Screen Wake Lock released:', wakeLock.released);
        });

        msg = 'wake lock on';
        statusMsg.text(msg);
        //console.log(msg);

    } catch (err) {
        msg = 'wake lock error: ' + `${err.name}, ${err.message}`;
        statusMsg.text(msg);
        console.error(msg);
    }
}

function releaseWakeLock()
{
    if (!hasWakeLock())
        return; // not available

    if (wakeLock) // is it locked?
    {
        wakeLock.release();
        wakeLock = null;

        $('.statusMsg').text('wake lock off');
    }
}

function resume()
{
	if (_paused)
	{
		_paused = false;
		deck.readSlideResume(); // picks up at curr	+ _lastCharIndex
	}
	else
	{
		// resuming without being paused means play was clicked from start panel
		startClock();
		deck.run(_incLine == 0); // if line has been inc'ing then don't start at the beginning.
	}

	$("#pause").show();
	$("#resume").hide();

	requestWakeLock();
}

function readPage(readText = '', textId = '')
{
    window.speechSynthesis.cancel();
    _readContinuous = false;

    if (_readPage)
    {
        // already reading...
        _readPage = false;
        $("#pause").hide();
        $("#readPage").show();
    }
    else
    {
        // start reading...
        _readPage = true; // stop after reading the current page

        if (readText.length == 0)
        {
            readText = deck.getText();
        }

        read(readText, 0, textId);

        $("#pause").show();
        $("#readPage").hide();
    }
}

function keepReading()
{
    var readFlag = $("#read_flag").val();
    var rc = false;
    if (readFlag == "once") // read once
    {
        rc = false;
    }
    else if (readFlag == "continuous" ) // read continuous
    {
        rc = true;
    }
    else
    {
        // check elapsed time
        var readSeconds = (readFlag * 60);
        rc = (getElapsedSeconds() < readSeconds); // readFlag is in minutes
    }

    return rc;
}

function isTimeExpired(readSeconds)
{
    var readFlag = $("#read_flag").val();
    var rc = false;
    if (readFlag == "once") // read once
    {
        rc = false;
    }
    else if (readFlag == "continuous" ) // read continuous
    {
        rc = false;
    }
    else
    {
        // check elapsed time
        var readSeconds = (readFlag * 60);  // readFlag is in minutes

        if (getElapsedSeconds() > readSeconds)
            rc = true;
    }

    return rc;
}

function getTimeTotalSeconds()
{
    var readFlag = $("#read_flag").val();
    var rc = 0;

    if (readFlag == "once") // read once
    {
        // no total seconds limit
    }
    else if (readFlag == "continuous" ) // read continuous
    {
        // no total seconds limit
    }
    else
    {
        // has a total time limit, return it in seconds
        rc = (readFlag * 60);  // readFlag is in minutes
    }

    return rc;
}

function runContinue()
{
	if (_incLine != 0) // if line selector was used (+-50) then use that
		curr = _incLine;
	else 			  // use last location from the session
		curr = parseInt(localStorage[deck.readLocationTag], 10);

	$("#pause").show();
	$("#resume").hide();
	startClock();
	requestWakeLock();
	deck.run(/* fromBeginning = */ false);
}

function runContinueOther()
{
	curr = deck.readLocationOtherDevice;

	$("#pause").show();
	$("#resume").hide();
	startClock();
	requestWakeLock();
	deck.run(/* fromBeginning = */ false);
}

function togglePause()
{
	if (_paused)
		resume();
	else
		pause();
}

function pause()
{
	_paused = true;
	window.speechSynthesis.cancel();

	$("#pause").hide();
    $("#resume").show();
    $("#readPage").show();

    releaseWakeLock();
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

    //debug("mute set to " + _mute.toString(), _debug);
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

var _speechTimerId = null;
var _clockTimerId = null;
var _pauseTimerId = null;
var _utter = null;
function read(text, charIndex, textId = '#slideDescription' /* used to highlight the current word */)
{
	_cancelled = false;
	clearTimeout(_speechTimerId);

	_utter = new SpeechSynthesisUtterance();
	_utter.volume = 1; // range is 0-1

	//_utter.rate = .75;  // range is 0-1, todo: make setable
   // _utter.rate = 1.0;

	if (deck.voice != null)
	{
        //console.log('reading: ' + deck.voice.lang);
		_utter.voice = deck.voice;  // if voices for language were found, then use the one we saved on start-up
		_utter.lang = deck.voice.lang;
	}
	else
	{
		_utter.lang = deck.language; // if voice not found, try to the language from the web site
	}

    if (false) // new
    {
        if (_voiceIndex < _voices.length)
        {
            _utter.voice = _voices[_voiceIndex];
            //console.log('reading: voice = ' + _utter.voice);
        }
        else
        {
            console.log('reading voices not loaded');
        }
    }

	_utter.text = text.substring(charIndex).toLowerCase();

	_utter.onend = function(event) {

        //console.log('onend - slide: ' + getCurrentId());
   	    touchReads(); // have to touch it here before it changes ids below

		if (!_readPage && !_paused && !_cancelled)
		{
			readNext();
		}
		else if (_readPage)
		{
		    // clear the word highlight
			$(textId + " span").removeClass("highlight-word");
    		_readPage = false; // finished reading page

            $("#pause").hide();
            $("#readPage").show();
		}

		_cancelled = false;
	}

	var wordIndex = -1;
	var charIndexPrev = -1;
	_utter.onboundary = function(event) {

		// Highlight browser support
		// Windows 10 - Edge
		// Windows 10 - Chrome (Microsoft voices only)
		// Windows 10 - Firefox

		// Android - Edge (case 2)
		// Android - Firefox
		// Android - Firefox Focus

		// MacBook - Safari
		// MacBook - Chrome
		// MacBook - Firefox

		// Not Supported:
		// Windows 10 - Chrome - Google Voices
		// Android - Chrome (only has Google voices, need to install more)
		// Android - TOR (no voices)```````````````````````````````````````````
		// Android - Opera (no voices)```````````````````````````````````````````

		if (event.name == "word")
		{
			var cases = -1;
			if (typeof event.charLength !== 'undefined')
			{
				if (event.charLength < text.length)
				{
					//case 1: charLength implemented correctly in browser
					cases = 1;
					var start = event.charIndex + charIndex;
					_lastCharIndex = start;
					var end = start + event.charLength;
					var word = text.substring(start, end);
					var before = (start > 0) ? text.substring(0, start) : "";
					var after = text.substring(end);
					$(textId).html(before + '<span class="highlight-word">' + word + '</span>' + after);
        			//console.log('charLength: ' + event.charLength);
				}
				else
				{
					//case 2: charLength exists but it's always set to length of the full text being read (Edge on Mobile)
					cases = 2;
				}
			}
			else
			{
				//case 3: charLength not implemented in browser
				cases = 2;
			}

			//debug("Case " + cases, _debug);
			if (cases != 1) // do it the hard way
			{
				var start = event.charIndex;
				_lastCharIndex = start;
				var word = text.substring(start);
				//debug(event.name + ': ' + word + ', index:' + event.charIndex + ", charLength: " + event.charLength, _debug);
				var words = word.split(" ");
				if (words.length > 0)
				{
					word = words[0];
					var before = (start > 0) ? text.substring(0, start) : "";
					var after = text.substring(start + word.length);
					$(textId).html(before + '<span class="highlight-word">' + word + '</span>' + after);
				}
			}

			//
			// make sure element is visible in the viewport
			//
			if ($('#tab1').is(':visible')) // only scroll when on the read tab, otherwise it scrolls the other tabs
				scrollTo('.highlight-word', _bottomPanelHeight); // has to be a class

			// case 4: onBoundary not implemented so highlighting isn't possible
		}
	}

	window.speechSynthesis.speak(_utter);
	_speechTimerId = setTimeout(speechBugWorkaround, 10000);
}

function speechBugWorkaround()
{
	//debug("reset speech", _debug);
	window.speechSynthesis.resume(); // fix to keep speech from stopping

	if (window.speechSynthesis.speaking)
	{
		clearTimeout(_speechTimerId);
		_speechTimerId = setTimeout(speechBugWorkaround, 10000);
	}
}

function readNext()
{
	curr++;

	if (curr >= max)
	{
		curr = 0;

		if (keepReading())
		{
		    pauseReadNext();
		}
        else
        {
        	saveReadLocation(0);
        	addHistory();
            end();
        }
	}
	else
	{
	    if (isTimeExpired())
	    {
	        end();
	    }
        else
        {
            pauseReadNext();
        }
	}
}

function pauseReadNext()
{
    var pauseSeconds = parseInt($(deck.pauseSecondsId).text());

    if (_pauseTimerId)
        clearTimeout(_pauseTimerId);

    if (pauseSeconds >= 0)
    {
        _pauseTimerId = setTimeout(deck.runSlide, pauseSeconds * 1000);
    }
}

function tts(text)
{
    if (!_mute)
    {
        var utter = new SpeechSynthesisUtterance();

        utter.lang = 'es-US';
        utter.text = text;

        window.speechSynthesis.speak(utter);
    }
}

function loadVoicesDeck()
{
    loadVoices(deck.language, deck.languageLong);

	if (_voices.length == 0 && _voicesLoadAttempts++ < 10)
	{
		console.log("loading voices...not ready");
		setTimeout(loadVoicesDeck, 500);
		return;
	}
}

function showSeconds(text = null)
{
    $(".showSeconds").text(text);
}

function end()
{
	clearTimeout(_speechTimerId);
	reset();
	loadData();
	deck.start();
	$("#pause").hide();
	$("#resume").show();
	$('#readCurrLine').text(deck.labelLine + " " + (curr + 1));
	showElapsedTime();
	clearTimeout(_clockTimerId);
	releaseWakeLock();
}

function reset()
{
	clear();
	curr = 0;
}

function clear()
{
    // clear slides
	deck.slides.forEach(function(slide, index){
	    slide.done = false;
	});
}

function loadSlide()
{
	saveReadLocation(curr);
	deck.setStates(RUNSTATE_RUN);
	deck.showSlide();
	updateLinks();
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

function updateLinks()
{
    // set up 'Go To Entry' and 'Delete Entry' links
    var id = getCurrentId();

    var href = '/' + deck.locale + '/definitions/edit-or-show/' + id;
	$('#goToEntry').attr("href", href);

    var href = '/' + deck.locale + '/definitions/confirmdelete/' + id;
	$('#deleteEntry').attr("href", href);

    console.log('href: ' + $('#goToEntry').attr("href"));
}

function touchReads()
{
    touch(true);
}

function touchViews()
{
    touch(false, true);
}

function touch(reads, views = false)
{
    // if it's a definition, update read count
    if (deck.touchPath !== null && deck.touchPath.length > 0) // if touchPath set
    {
        var path = deck.touchPath + '?definition_id=' + getCurrentId();

        if (reads)
        {
            path += '&reads=1';
        }

        if (views)
        {
            path += '&views=1';
        }

        if (reads || views)
            ajaxexec(path); // touch it and update stats

        console.log('reader::touch() ajax path: ' + path);
    }
    else
    {
    	console.log('reader::touch() path not set');
    }
}

var _dictionary = "_blank";
var _selectedWordThrottle = ""; // used to slow down ajax definition calls for selected words
function getSelectedText(clicks)
{
	pause();

    var text = "";
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection && document.selection.type != "Control") {
        text = document.selection.createRange().text;
    }

    // clean text
	text = text.trim();

    // clean double-quotes
    text = text.replace(/"/g, '\\x22');
    text = text.replace(/“/g, '\\x22');
    text = text.replace(/”/g, '\\x22');
    text = text.replace(/„/g, '\\x22');

    // clean single-quotes
    text = text.replace(/`/g, '\\x27');
    text = text.replace(/'/g, '\\x27');
    text = text.replace(/❛/g, '\\x27');

	if (text.length > 0)
	{
		if (text == _selectedWordThrottle)
		{
			// throttle the lookups because they come on mouseup for selections and dblclick for word selection
			setTimeout(function(){	_selectedWordThrottle = ""; /*console.log('cleared throttle');*/ }, 500);

			return;
		}
		//console.log('sent ajax for: ' + text);
		_selectedWordThrottle = text;

		// copy selected text
		var succeed;
		try
		{
			succeed = document.execCommand("copy");
		}
		catch(e)
		{
			succeed = false;
		}

		var html = "<div style='margin-bottom:10px;'><span style='font-size:1.2em;'>" + text + "</span>"
			+ "&nbsp;<a target='_blank' href='https://deepl.com/translator#es/en/" + text + "'>(DeepL)</a>"
			+ "&nbsp;<a target='_blank' href='https://www.spanishdict.com/translate/" + text + "'>(SpanDict)</a>"
			+ "&nbsp;<a target='_blank' href='https://dle.rae.es/" + text + "'>(RAE)</a>";
			if (true)
			{
				//orig: html += "&nbsp;<a target='_blank' href='/dictionary/create-quick/" + text + "'>(add)</a>";
				html += "&nbsp;<a href='' onclick='event.preventDefault(); ";
				html += 'ajaxexec("/dictionary/create-quick/' + text + '"); $("#selected-word").html("<p>Added</p>"); \'>(add)</a>';
				//console.log(html);
			}
			html+= "</div>";

		//_hotWords.push(text + ": ");
		$('#selected-word').html(html);
		$('#selected-word-definition').text('');

		// check the dictionary for the selected text
		if (deck.contentId > 0)
		{
            var url = '/definitions/get/' + text + '/' + deck.contentId;
            ajaxexec(url, '#selected-word-definition', false, translateCallback);
		}
		else
		{
            console.log('content id not set: ' + deck.contentId);
		}
	}
}

function removeDefinitionUser(url)
{
	ajaxexec(url, '', false, translateCallback);
}

function translateCallback(definition)
{
	//translator not plugged in: ajaxexec('/entries/get-definitions-user/' + parseInt(deck.contentId, 10) + '', '#defs');
}

function xlate(word)
{
	$('#selected-word-definition').text('translating...');
	ajaxexec('/definitions/translate/' + word + '/' + deck.contentId + '', '#selected-word-definition', false, translateCallback);
}

function zoom(event, amount)
{
	event.preventDefault();

	//var size = $("#slideDescription").css("font-size");
	_readFontSize += amount;

	if (_readFontSize > _maxFontSize) // don't go crazy
		_readFontSize = _maxFontSize;
	if (_readFontSize < _minFontSize) // don't go crazy
		_readFontSize = _minFontSize;
	//console.log('font size: ' + _readFontSize);

	localStorage['readFontSize'] = _readFontSize;
	setFontSize();
}

function setFontSize()
{
	$("#slideDescription").css("font-size", _readFontSize + "px");

    var fontDiff = 5;
	var transFontSize = _readFontSize - fontDiff;
	transFontSize = transFontSize >= (_minFontSize - fontDiff) ? transFontSize : _minFontSize;
	$("#slideTranslation").css("font-size", transFontSize + "px");
	//console.log('transFont: ' + transFontSize);

	$("#slideTitle").css("font-size", _readFontSize + "px");
	$("#readFontSizeLabel").css("font-size", _readFontSize + "px");
	$(".glyph-zoom-button").css("font-size", _readFontSize + "px");
	$("#readFontSize").text(_readFontSize);
}

function saveReadLocation(location)
{
    //console.log('saving readlocation: ' + location);
	localStorage[deck.readLocationTag] = location;
	if (location == 0)
	{
		$('#button-continue-reading').hide();
		$('#button-start-reading').text(deck.labelStart); //"Start Reading");
	}

    var recordId = parseInt(deck.contentId, 10);
	if (deck.userId > 0 && recordId > 0) // if logged in, save read location in db
	{
		ajaxexec('/entries/set-read-location/' + recordId + '/' + location + '/');
    	deck.readLocationOtherDevice = location;
	}
}

function getReadLocation()
{
	var location = parseInt(localStorage[deck.readLocationTag], 10);
	var multipleLocations = (location != deck.readLocationOtherDevice);

	if (location > 0 && location < max)
	{
		$('#button-start-reading').text(deck.labelStartBeginning); //"Start reading from the beginning"
		$('#button-continue-reading').show();
		$('#button-continue-reading').text(deck.labelContinue + " " + (location + 1));
	}

	if (multipleLocations && deck.readLocationOtherDevice > 0 && deck.readLocationOtherDevice < max)
	{
		$('#button-start-reading').text(deck.labelStartBeginning);
		$('#button-continue-reading').html(deck.labelContinue + " " + (location + 1) + ""); // "<br/><span class='small-thin-text'>(location on this device)</span>");

		$('#button-continue-reading-other').show();
		$('#button-continue-reading-other').html(deck.labelContinue + " " + (deck.readLocationOtherDevice + 1) + "<br/><span class='small-thin-text'>(" + deck.labelLocationDifferent + ")</span>");
	}

	$('#readCurrLine').text(deck.labelLine + " " + (curr + 1));
	//debug("getReadLocation: " + location, _debug);
}

//<div id="panel-run-col-defs" class="col-md-4 mt-3" style="background-color:white; padding:0;">
//<div id="panel-run-col-text" class="col-md-8" style="" >
function toggleShowDefinitions()
{
	if ($('#panel-run').is(':visible'))
	{
		if ($('#panel-run-col-defs').is(':visible'))
		{
			$('#panel-run-col-defs').hide();
			$('#panel-run-col-defs').removeClass('col-md-4');
			$('#panel-run-col-text').removeClass('col-md-8');
		}
		else
		{
			$('#panel-run-col-defs').show();
			$('#panel-run-col-defs').addClass('col-md-4');
			$('#panel-run-col-text').addClass('col-md-8');
		}
	}
}

function startClock()
{
	_startTime = new Date();
	clearTimeout(_clockTimerId);
	_clockTimerId = setTimeout(showElapsedTime, 1000);
}

function showElapsedTime()
{
	var time = getElapsedTime();
	$('#elapsedTime').text(deck.labelReadingTime + ": " + time);
	$('#clock').text(time);

	clearTimeout(_clockTimerId);
	_clockTimerId = setTimeout(showElapsedTime, 1000);
}

function getElapsedSeconds()
{
    var seconds = 0;

	// get run time
	if (_startTime != null)
	{
		endTime = new Date();
		var timeDiff = endTime - _startTime; //in ms
		timeDiff /= 1000; // to seconds
		seconds = Math.round(timeDiff);
    }

    return seconds;
}

function getElapsedTime()
{
	var time = '';

	// get run time
	if (_startTime != null)
	{
		var seconds = getElapsedSeconds();

        time = getTimeDisplay(seconds);

        var totalSeconds = getTimeTotalSeconds();
        if (totalSeconds > 0)
            time += ' / ' + getTimeDisplay(totalSeconds);
	}

	return time;
}

function getTimeDisplay(seconds)
{
    var time = '';

    if (seconds < 10)
        time = '00:0' + seconds;
    else
        time = '00:' + seconds

    if (seconds >= 60)
    {
        minutes = Math.round(seconds / 60);
        seconds = seconds % 60;

        if (minutes >= 70)
        {
            hours = Math.round(minutes / 60);
            minutes = minutes % 60;

            if (minutes < 10)
                minutes = "0" + minutes;
            if (seconds < 10)
                seconds = "0" + seconds;
            if (hours < 10)
                hours = "0" + hours;

            time = hours + ":" + minutes + ":" + seconds;
        }
        else
        {
            if (minutes < 10)
                minutes = "0" + minutes;
            if (seconds < 10)
                seconds = "0" + seconds;

            time = minutes + ":" + seconds;
        }
    }

    return time;
}

function addHistory()
{
    addHistoryRecord(deck.historyPath, deck.programName, deck.programId, deck.programType, deck.programSubType, deck.programAction, deck.sessionName, deck.sessionId, max, deck.historyRoute);
}
