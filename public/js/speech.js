var _voices = null;
var _voicesLoadAttempts = 0;

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
	setTimeout(loadVoicesGlobal, 500);

    console.log('speech.js ready');
});

function getLanguageIndex()
{
	var e = document.querySelector('#language_flag');
    var index = e.options[e.selectedIndex].value;
    //index = (index >= 0) ? index : 0;

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
    var index = getLanguageIndex();
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
    console.log('language: ' + language);
    console.log('loading voices...');

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

        // quick check to see if there are any matches
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

        console.log('voices: ' + _voices.length);
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
	// set the active voice from the select dropdown
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

	var voice = $("#selectVoice").children("option:selected").val();
	//orig: deck.voice = _voices[voice];
	//orig: if (_utter != null)
	{
		//orig: _utter.voice = deck.voice;
	}

	//$("#language").text("Language: " + deck.voice.lang + ", voice: " + deck.voice.name);
}
