var _voices = null;
var _voicesLoadAttempts = 0;
var _voiceIndex = 0;

var _languages = [
    'en-EN',    // 0
    'es-ES',    // 1
    'fr-FR',    // 2
    'it-IT',    // 3
    'de-DE',    // 4
    'pt-PT',    // 5
    'ru-RU',    // 6
    'zh-CN',    // 7
    'ko-KR'     // 8
];

var _languagesLong = [
    'eng-GBR',    // 0
    'spa-ESP',    // 1
    'fra-FRA',    // 2
    'ita-ITA',    // 3
    'get-GER',    // 4
    'por-POR',    // 5
    'rus-RUS',    // 6
    'chi-CHI',    // 7
    'kor-KOR'     // 8
];

$(document).ready(function() {

	window.speechSynthesis.cancel();

	if (typeof(deck) != "undefined")
	    setTimeout(loadVoicesGlobal, 500);

    console.log('speech.js ready');
});

function getLanguageIndex()
{
    var index = -1; // not set
	var e = document.querySelector('#language_flag');
	if (e != undefined)
	{
        index = e.options[e.selectedIndex].value;
	}

    return index;
}

function setLanguageGlobal()
{
    var path = '/setlanguage/' + getLanguageIndex();
    ajaxexecreload(path);

    //loadVoicesGlobal();
}

function loadVoicesGlobal()
{
    //console.log('loadVoicesGlobal');

    var index = getLanguageIndex();
    //console.log('language index: ' + index);
    if (index < 0) // not set, don't load from global select
    {
        //console.log('loadVoicesGlobal: voice selector not found');
        loadVoicesDeck(); //todo: fix the flow
        return;
    }

    if (index >= 100) // all language so set to english
    {
        index = 0;
    }

    var language = _languages[index];
    var languageLong = _languagesLong[index];

    loadVoices(language, languageLong);

	if (_voices.length == 0 && _voicesLoadAttempts++ < 10)
	{
		console.log("loading voices...not ready");
		setTimeout(loadVoicesGlobal, 500);
		return;
	}
}

function loadVoices(language, languageLong)
{
    //console.log('language: ' + language);
    //console.log('loading voices...');

	_voices = window.speechSynthesis.getVoices();

	if (_voices.length == 0 && _voicesLoadAttempts++ < 10)
	{
		return;
	}

	//tts('ready with ' + _voices.length + ' voices');

	var voiceSelect = document.querySelector('#selectVoice');

    // empty the voices from the select
    var length = voiceSelect.options.length;
    for (i = length-1; i >= 0; i--) {
        voiceSelect.options[i] = null;
    }

	var found = 0;

	if (_voices.length > 0)
	{
	    var langCodeSize = 2;
	    var deckLang = language.substring(0, langCodeSize);

	    // figure out how the voices are formatted, either 'en-US' or 'eng-USA', 'es-ES' or 'spa-ESP'
	    if (_voices.length > 0 && _voices[0].lang.length > 5)
	    {
	        // using 3 letter language and country codes: 'spa-ESP'
	        langCodeSize = 3;
    	    deckLang = languageLong.substring(0, langCodeSize);
	    }

        // quick check to see if there are any voices installed for the selected language
        var showAll = true;
		for(i = 0; i < _voices.length ; i++)
		{
            var lang = _voices[i].lang.substring(0, langCodeSize);
            if (deckLang == lang)
            {
                // if at least one found, bail out
                showAll = false;
                break;
            }
		}

        // load the voices into the select for the specified language OR all
        //console.log('voices: ' + _voices.length);
		for(i = 0; i < _voices.length ; i++)
		{
			var option = document.createElement('option');
			option.textContent = _voices[i].name;
			if (option.textContent.length < 10 && !option.textContent.endsWith(')')) // if it's short and doesn't already have something in parens, add the language
    			option.textContent += ' (' + _voices[i].lang + ')';
			option.value = i;

			if(_voices[i].default) {
			  //option.textContent += ' (default)';
			}

			option.setAttribute('data-lang', _voices[i].lang);
			option.setAttribute('data-name', _voices[i].name);

            var lang = _voices[i].lang.substring(0, langCodeSize);
            //console.log('looking for: ' + deckLang + ', voice: ' + lang);

            if (showAll || deckLang == lang)
            {
                if (found == 0)
                {
                    found++;
                }

                voiceSelect.appendChild(option);
            }
		}
	}
	else
	{
		var option = document.createElement('option');
		option.textContent = "Default voice set: " + language;
		voiceSelect.appendChild(option);
	}

	//
	// set the active voice from local storage OR to 0
	//
	if (found)
	{
		setSelectedVoice(voiceSelect);
		changeVoice();
	}
	else
	{
		msg = "Language not found: " + language + ", text can't be read correctly.";
		$("#language").text(msg);
		$("#languages").show();
	}
}

function saveSelectedVoice(voiceIndex)
{
	localStorage['readVoiceIndex'] = voiceIndex;
	//debug("set readVoiceIndex: " + voiceIndex, _debug);
}

function setSelectedVoice(voiceSelect)
{
	var voiceIndex = localStorage['readVoiceIndex'];
	if (!voiceIndex)
	{
		localStorage['readVoiceIndex'] = 0;
		voiceIndex = 0;
	}

	voiceSelect.selectedIndex = (voiceIndex < voiceSelect.options.length) ? voiceIndex : 0;
	//debug("get: readVoiceIndex: " + voiceIndex, _debug);
}

function changeVoice()
{
	var index = $("#selectVoice")[0].selectedIndex;
	saveSelectedVoice(index);

	var voiceIndex = $("#selectVoice").children("option:selected").val();
	voice = _voices[voiceIndex];

	if (typeof(deck) != "undefined")
	    deck.voice = voice;

	if (_utter != null)
	{
		_utter.voice = voice;
	}

    _voiceIndex = index;
    //console.log('reading voice set to: ' + index);

	//$("#language").text("Language: " + deck.voice.lang + ", voice: " + deck.voice.name);
}
