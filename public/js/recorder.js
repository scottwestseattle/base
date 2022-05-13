// set up basic variables for app

var record = null;
var stop = null;
var soundClips = null;
var canvas = null;
var mainSection = null;

// visualiser setup - create web audio api context and canvas
let audioCtx = null;
var canvasCtx = null;

$(document).ready(function() {
    console.log('recorder.js ready');
});

///////////////////////////////////////////////////
// main block for doing the audio recording
///////////////////////////////////////////////////

const constraints = { audio: true };
var _audio = null;
var _mediaRecorder = null;

function startMediaRecorder()
{
    console.log('startMediaRecorder()...');

if (navigator.mediaDevices.getUserMedia) {
  console.log('getUserMedia supported.');

  let chunks = [];

  let onSuccess = function(stream) {

    console.log('onSuccess');

    _mediaRecorder = new MediaRecorder(stream);

    visualize(stream);

    _mediaRecorder.onstop = function(e) {
      console.log("data available after MediaRecorder.stop() called.");

      //const clipName = prompt('Enter a name for your sound clip?','My unnamed clip');
      const clipName = null;

      var clipContainer = document.querySelector('article');
      if (clipContainer == null)
        clipContainer = document.createElement('article');

      const clipLabel = document.createElement('p');
      const audio = document.createElement('audio');
      const deleteButton = document.createElement('button');
      const playButton = document.createElement('button');

      clipContainer.classList.add('clip');
      audio.setAttribute('controls', '');
      deleteButton.textContent = 'Delete';
      deleteButton.className = 'playback';
      deleteButton.id = 'playbackDelete';
      playButton.textContent = 'Play';
      playButton.className = 'playback';
      playButton.id = 'playbackPlay';

      if(clipName === null) {
        clipLabel.textContent = '';
      } else {
        clipLabel.textContent = clipName;
      }

      clipContainer.appendChild(audio);
      clipContainer.appendChild(clipLabel);
      clipContainer.appendChild(deleteButton);
      clipContainer.appendChild(playButton);
      soundClips.appendChild(clipContainer);

      audio.controls = true;
      console.log('chunks: ' + chunks.length);

      const blob = new Blob(chunks, { 'type' : 'audio/ogg; codecs=opus' });
      console.log('blob created: ' + blob);

      chunks = [];
      const audioURL = window.URL.createObjectURL(blob);
      audio.src = audioURL;
      console.log("recorder stopped");

      deleteButton.onclick = function(e) {
        e.preventDefault();
        let evtTgt = e.target;
        evtTgt.parentNode.parentNode.removeChild(evtTgt.parentNode);
        _audio = null;
      }

      playButton.onclick = function(e) {
        e.preventDefault();
        audio.play().catch(function (error) {
            console.log('audio play from button error: ' + error.message);
        });
      }

      clipLabel.onclick = function() {
        const existingName = clipLabel.textContent;
        const newClipName = prompt('Enter a new name for your sound clip?');
        if(newClipName === null) {
          clipLabel.textContent = existingName;
        } else {
          clipLabel.textContent = newClipName;
        }
      }

      audio.play().catch(function (error) {
        console.log('audio play error: ' + error.message);
      });

      _audio = audio;
    }

    _mediaRecorder.ondataavailable = function(e) {
      chunks.push(e.data);
    }

    setTimeout(runRecord, 500)

    console.log('end of onSuccess');
  } // end of onSuccess

  let onError = function(err) {
    console.log('The following error occured: ' + err);
  }

    console.log('starting getUserMedia... ');

    // this triggers the permission dialog
    navigator.mediaDevices.getUserMedia(constraints).then(onSuccess, onError);

    console.log('end of getUserMedia init');

} else {
   console.log('getUserMedia not supported on your browser!');
}

} // end of function

function startRecording()
{
    console.log('startRecording...');

    if (_mediaRecorder == null)
    {
        setRecordButton("Waiting...", "orange", "orange", /* recording = */ true);
        startMediaRecorder();
    }
    else
    {
        runRecord();
    }
}

function runRecord()
{
    //console.log('runRecord...');

    if (_mediaRecorder.state == "inactive")
    {
        var clips = document.querySelector('article');
        if (clips)
        {
            clips.innerHTML = "";
        }

        try {
            _mediaRecorder.start();
            //console.log(_mediaRecorder.state);
            //console.log("recordering...");
            setRecordButton("Stop", "red", "red", /* recording = */ true);
        }
        catch(e)
        {
            console.log(_mediaRecorder.state);
            console.log('record start error: ' + e)
        }
    }
    else
    {
        try {
            _mediaRecorder.stop();
            //console.log(_mediaRecorder.state);
            //console.log("recorder started");
            setRecordButton("Record", "#0088cc", "#4993FD", /* recording = */ false);
        }
        catch(e)
        {
            console.log(_mediaRecorder.state);
            console.log(e)
        }
    }
}

function setRecordButton(text, color, colorGlyph, recording = false)
{
    if (record)
    {
        record.style.background = color;
        record.textContent = "Record";
    }
    else
    {
        $('#buttonRecordGlyph a').css("color", colorGlyph != null ? colorGlyph : color);
    }

    if (recording)
    {
        $('#feedback').show();
    }
    else
    {
        $('#feedback').hide();
    }
}


function visualize(stream) {
  if(!audioCtx) {
    audioCtx = new AudioContext();
  }

  const source = audioCtx.createMediaStreamSource(stream);

  const analyser = audioCtx.createAnalyser();
  analyser.fftSize = 2048;
  const bufferLength = analyser.frequencyBinCount;
  const dataArray = new Uint8Array(bufferLength);

  source.connect(analyser);
  //analyser.connect(audioCtx.destination);

  draw()

  function draw() {
    const WIDTH = canvas.width
    const HEIGHT = canvas.height;

    requestAnimationFrame(draw);

    analyser.getByteTimeDomainData(dataArray);

    canvasCtx.fillStyle = 'rgb(255, 255, 255)'; // white
    //canvasCtx.fillStyle = 'rgb(200, 200, 200)'; // orig gray
    canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);

    canvasCtx.lineWidth = 2;
    canvasCtx.strokeStyle = 'red';
    //canvasCtx.strokeStyle = 'rgb(0, 0, 0)'; // orig black

    canvasCtx.beginPath();

    let sliceWidth = WIDTH * 1.0 / bufferLength;
    let x = 0;


    for(let i = 0; i < bufferLength; i++) {

      let v = dataArray[i] / 128.0;
      let y = v * HEIGHT/2;

      if(i === 0) {
        canvasCtx.moveTo(x, y);
      } else {
        canvasCtx.lineTo(x, y);
      }

      x += sliceWidth;
    }

    canvasCtx.lineTo(canvas.width, canvas.height/2);
    canvasCtx.stroke();

  }
}

window.onresize = function() {

    mainSection = (mainSection === null) ? document.querySelector('.main-controls') : mainSection;
    canvas.width = mainSection.offsetWidth;
}

function loadRecorder()
{
    //console.log('loading recorder...');

    // record and play are optional
    record = document.querySelector('#buttonRecord');

    soundClips = document.querySelector('.sound-clips');
    mainSection = document.querySelector('.main-controls');

    // the feedback canvas widget
    canvas = document.querySelector('.visualizer');
    canvasCtx = canvas.getContext("2d");

    // size our window
    window.onresize();
}

function playRecording()
{
    if (_audio != null)
        _audio.play().catch(function (error) {
        console.log('audio play from big button error: ' + error.message);
      })
}

function copyToReader(event, fromId, toId, scrollClass)
{
    var id = fromId;
    fromId = '#' + fromId;

    event.preventDefault();
    //console.log('from: ' + $(fromId).val());
    $(toId).val($(fromId).val());

	var e = $(scrollClass).first();
	var position = e.offset();
    window.scroll(position.left, position.top - 60);

    // set the cookie to keep it active
	ajaxexec('/snippets/cookie/' + id + '');
}
