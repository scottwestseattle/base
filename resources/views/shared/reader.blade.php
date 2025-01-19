@extends('layouts.reader')
@section('title', __('Reading') . ' ' . $title )
@section('content')
@php
    $locale = app()->getLocale();
    $recordId = isset($recordId) ? $recordId : -1;
    $readLocation = isset($readLocation) ? $readLocation : null;
    $count = isset($options['count']) ? $options['count'] : count($lines['text']);
	$hasTranslation = isset($lines['translation']);// && count($lines['text']) == count($lines['translation']); // translation matches text
	$showTranslationControls = isset($lines['translation']); // show the translation in case it needs work
	$mobileOnly = false && isMobile() ? '' : 'hidden'; // off for now
    $isDefinition = true;
    $pauseSecondsId = "pause_seconds_definitions";
    $pauseSecondsIdPound = '#' . $pauseSecondsId;
	if ($contentType === 'Entry') // save 'pause seconds' separately for entries and definitions
	{
	    // articles or books
	    $isDefinition = false;
	    $pauseSecondsId = "pause_seconds_entry";
        $pauseSecondsIdPound = '#' . $pauseSecondsId;
	}
	else
	{
	    // snippets or definitions: use default values set above
	}

    // read options
    $randomOrder = isset($options['randomOrder']) ? $options['randomOrder'] : false;
    $readRandom = isset($options['readRandom']) ? $options['readRandom'] : false;
    $randomOrder = $randomOrder || $readRandom;
    $readReverse = isset($options['readReverse']) ? $options['readReverse'] : false;
    $readPrompts = isset($options['readPrompts']) ? $options['readPrompts'] : false;
@endphp

<!-------------------------------------------------------->
<!-- Add misc data needed by the JS during runtime -->
<!-------------------------------------------------------->
<div class="data-misc"
    data-title="{{$title}}"
	data-count="{{$count}}"
	data-max="{{$count}}"
	data-touchpath="{{(isset($options['touchPath']) ? $options['touchPath'] : '')}}"
	data-language="{{$languageCodes['short']}}"
	data-language-long="{{$languageCodes['long']}}"
	data-type="{{$contentType}}"
	data-contenttype="{{$contentType}}"
	data-pauseSecondsId="{{$pauseSecondsIdPound}}"
	data-contentid="{{$recordId}}"
	data-isadmin="{{isAdmin() ? 1 : 0}}"
	data-userid="{{Auth::id()}}"
	data-readlocation="{{$readLocation}}"
	data-useKeyboard="1"
	data-labelstart="{{$labels['start']}}"
	data-labelstartbeginning="{{$labels['startBeginning']}}"
	data-labelcontinue="{{$labels['continue']}}"
	data-labellocationdifferent="{{$labels['locationDifferent']}}"
	data-labelline="{{$labels['line']}}"
	data-labelof="{{$labels['of']}}"
	data-labelreadingtime="{{$labels['readingTime']}}"
	data-randomorder="{{$randomOrder ? 1 : 0}}"
	data-locale="{{app()->getLocale()}}"
    @component('components.history-parameters', ['history' => $history])@endcomponent
></div>

	<!-------------------------------------------------------->
	<!-- Add the body lines to read -->
	<!-------------------------------------------------------->
@foreach($lines['text'] as $line)
    @php
        $id = isset($lines['ids'][$loop->index]) ? $lines['ids'][$loop->index] : -5;
        $translation = ($hasTranslation && isset($lines['translation'][$loop->index])) ? $lines['translation'][$loop->index] : '';
    @endphp
	<div class="data-slides"
	    data-title="{{$line}}"
	    data-number="1"
	    data-description="{{$line}}"
	    data-translation="{{$translation}}"
	    data-id="{{$id}}"
	    data-seconds="10"
	    data-between="2"
	    data-countdown="1"
	></div>
@endforeach

<div class="container">
	<!-------------------------------------------------------->
	<!-- Header -->
	<!-------------------------------------------------------->
	<div class="text-center m-2" style="">

		<!-------------------------------------------------------->
		<!-- Top Row Buttons -->
		<!-------------------------------------------------------->
		<div class="" style="row-height:50px; background-color:default;">
			<span class="glyphReader" style="">
		        <a href="{{route('home.frontpage', ['locale' => $locale])}}"><span style="margin-right:3px; font-size:.9em;" class="glyphicon glyphicon-home"></span></a>
            </span>
			<span class="glyphReader">
                <a href="{{$options['return']}}"><button type="button" class="btn btn-sm btn-primary mb-2"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-up"></span>{{trans_choice('ui.Return', 1)}}</button></button></a>
			</span>

			@if (isset($options['viewUrl']))
                <span class="glyphReader" style="">
                    <a href="{{$options['viewUrl']}}"><span style="margin-right:3px; font-size:.9em;" class="glyphicon glyphicon-list"></span></a>
                </span>
            @else
    			<span class="glyphReader"><a onclick="event.preventDefault(); reload()" href=""><span id="button-repeat" class="glyphicon glyphicon-repeat"></span></a></span>
            @endif

			<span class="glyphReader"><a onclick="zoom(event, -3);" href=""><span class="glyphicon glyphicon-zoom-out"></span></a></span>
			<span class="glyphReader"><a onclick="zoom(event, 3);" href=""><span class="glyphicon glyphicon-zoom-in"></span></a></span>
			<span class="glyphReader"><a onclick="event.preventDefault(); $('#settings').toggle();" href=""><span class="glyphicon glyphicon-cog"></span></a></span>
			@if (false && Auth::check())
				<span class="glyphReader"><a onclick="toggleActiveTab(event, '#tab2', '#tab1', '.tab-body');" href=""><span class=""></span></a></span>
			@endif
			@if (false && $hasTranslation) // old way
				<span class="glyphReader"><a onclick="event.preventDefault(); deck.showTranslation();" href=""><span class="glyphicon glyphicon-text-width"></span></a></span>
			@endif

			@if (false)
			<span class="glyphReader"><a onclick="setActiveTab(event, '#tab1', '.tab-body');" href=""><span class="glyphicon glyphicon-volume-up"></span></a></span>
			@endif
		</div>
	</div>

	<!--------------------------------------------------------------->
	<!-- tab 1 ------------------------------------------------------>
	<!--------------------------------------------------------------->
	<div id="tab1" class="tab-body">
		<!---------------------------------------------------------------------------------------------------------------->
		<!-- Start Panel - Index -->
		<!---------------------------------------------------------------------------------------------------------------->
		<div id="panel-start" class="slide-panel">

			<div class="text-center">
				<div class="small-thin-text">{{$count}} {{strtolower(trans_choice('ui.Line', 2))}}</div>
				<div id="slideTitle" style="font-size:18px" class="mb-2">{{$title}}</div>
				<a onclick="event.preventDefault(); run()"  href="" class="btn btn-primary mb-3"  id="button-start-reading" role="button">{{__('proj.Start Reading')}}</a>
				<div><a onclick="event.preventDefault(); runContinue()"  href="" class="btn btn-success mb-3" id="button-continue-reading" style="display:none;" role="button">{{__('proj.Continue reading from line')}}</a></div>
				<div><a onclick="event.preventDefault(); runContinueOther()"  href="" class="btn btn-warning mb-3" id="button-continue-reading-other" style="display:none;" role="button">{{__('proj.Continue reading from location on other device')}}</a></div>
				<div>
				    @if (isAdmin())
                        <a onclick="incLine(event, -50)" href=""><span id="button-decrement-line" class="glyphicon glyphCustom glyphicon-minus-sign"></span></a>
                        <div id="readCurrLine" class="middle large-text mb-2" style="min-width:85px;">{{trans_choice('ui.Line', 2)}} </div>
                        <a onclick="incLine(event, 50)" href=""><span id="button-increment-line" class="glyphicon glyphCustom glyphicon-plus-sign"></span></a>
    					<div class="statusMsg" class="ml-3"></div>
                    @endif
					<div id="elapsedTime" class="mt-5"></div>
				</div>

			</div>

		</div><!-- panel-start -->

		<!---------------------------------------------------------------------------------------------------------------->
		<!-- Run Panel -->
		<!---------------------------------------------------------------------------------------------------------------->

		<div id="run-panel" class="container-fluid" style="xpadding-bottom:60px;"><!-- padding is to make the elements scroll correctly -->
		  <div class="row">
			<div id="panel-run-col-text" class="" style="width:100%;" >
				<div id="panel-run" class="slide-panel text-center" style="">
					<div><span class="medium-thin-text" id="title">{{$title}}</span></div>
					<div class="small-thin-text">
						<span id="slideCount"></span>
						<span id="clock" class="ml-2">00:00</span>
						@if ($isDefinition)
						    <span class="ml-2"><a id="goToEntry" href="" target="_blank">{{__('proj.Go To Entry')}}</a></span>
						    <span class="ml-2"><a id="deleteEntry" href="" target="_blank">{{__('base.Delete Entry')}}</a></span>
						@endif
						<span class="statusMsg ml-3 {{$mobileOnly}}">Wake not set</span>
					</div>

					<div id="debug"></div>
					<div id="slideDescription" class="slideDescription" style="font-weight:300; font-size: 18px;" onmouseup="getSelectedText(1);" ondblclick="getSelectedText(2);" ontouchend="getSelectedText();"></div>
					<div class="" style="color: green;" id="selected-word"></div>
					<div class="" style="color: green;" id="selected-word-definition"></div>
                    @if ($hasTranslation)
    					<div style="font-size:.8em;"><a href="" onclick="event.preventDefault(); pause(); deck.showTranslation();">{{trans_choice('ui.Show Translation', 1)}}</a></div>
                        <div id="slideTranslation" class="mt-2 steelblue hidden" style="font-size: 17px;" ></div>
					@endif
				</div><!-- panel-run -->
			</div>
		  </div>
		</div>

	</div>

	<!--------------------------------------------------------------->
	<!-- tab 2 ------------------------------------------------------->
	<!--------------------------------------------------------------->
	<div id="tab2" class="tab-body" style="display:none;">
		<div id="panel-run-col-defs" class="mt-3">
			<div id="defs" style=""></div>
		</div>
	</div>

	<!--------------------------------------------------------------->
	<!-- Settings panel popup   ------------------------------------->
	<!--------------------------------------------------------------->
	<div id="settings" class="overlay" style="display:default;">
		<div class="text-center mt-3">
			<div class="mb-2">
                <label for="languages" class="checkbox-big-label mb-0">{{trans_choice('ui.Voice', 1)}}:</label>
                <div name="languages" id="languages" class="middle center" style="">
                    <select class="" onchange="changeVoice();" name="selectVoice" id="selectVoice"></select>
                </div>
            </div>
			<div class="mb-2">
                <label for="" class="checkbox-big-label mb-0">{{trans_choice('proj.Prompt Voice', 1)}}:</label>
                <div name="" id="" class="middle center" style="">
                    <select class="" onchange="changePromptVoice();" name="selectPromptVoice" id="selectPromptVoice"></select>
                </div>
            </div>
            <div class="m-0" style="">
                <label for="read_flag" class="checkbox-big-label" style="margin:0;">@LANG('ui.Read'):</label>
                <select name="read_flag" id="read_flag">
                    <option value="once">@LANG('proj.Once')</option>
                    <option value="1">1 {{trans_choice('ui.minute', 1)}}</option>
                    <option value="5">5 {{trans_choice('ui.minute', 2)}}</option>
                    <option value="10">10 {{trans_choice('ui.minute', 2)}}</option>
                    <option value="15">15 {{trans_choice('ui.minute', 2)}}</option>
                    <option value="30">30 {{trans_choice('ui.minute', 2)}}</option>
                    <option value="45">45 {{trans_choice('ui.minute', 2)}}</option>
                    <option value="60">60 {{trans_choice('ui.minute', 2)}}</option>
                    <option value="continuous">@LANG('proj.Continuous')</option>
                </select>
            </div>
            <hr />
            <div class="m-0">
                <div>@LANG('proj.Seconds to pause between lines'):</div>
                <a onclick="inc(event, '{{$pauseSecondsIdPound}}', -1)" href=""><span class="glyphicon glyphCustom glyphicon-minus-sign"></span></a>
                <div class="middle mb-2" style="min-width:30px;"><span class="ml-2 mr-2" style="font-size:25px;" id="{{$pauseSecondsId}}">0</span></div>
                <a onclick="inc(event, '{{$pauseSecondsIdPound}}', 1)" href=""><span class="glyphicon glyphCustom glyphicon-plus-sign"></span></a>
            </div>
            <hr />
            <div class="m-0">
                <div>@LANG('proj.Line Order'):</div>
                <input type="radio" id="random_order" name="random_order" value="0" {{$randomOrder ? '' : 'checked'}}>
                <label for="random_order">@LANG('ui.Default')</label><br>
                <input type="radio" id="random_order" name="random_order" value="1" {{$randomOrder ? 'checked' : ''}}>
                <label for="random_order">@LANG('ui.Random')</label><br>
            </div>

            @if ($hasTranslation || $showTranslationControls)
                <hr />

                @if ($hasTranslation)
                    <div id="" class="mt-2 steelblue" style="">
                        <span class="glyphicon glyphCustom glyphicon-text-width mr-2"></span>
                    </div>
                @endif

                @if ($showTranslationControls)
                <div>
                    <div class="mt-1 ml-1">
                        <input type="checkbox" name="checkbox-flip" id="checkbox-flip" style="height:20px; position:static;" {{$readReverse ? 'checked' : ''}} />
                        <label for="checkbox-flip" class="checkbox-sm steelblue" onclick="">@LANG('proj.Reverse text and translation')</label>
                    </div>
                    <div class="mt-1 ml-1">
                        <input type="checkbox" name="checkbox-read-prompts" id="checkbox-read-prompts" style="height:20px; position:static;" {{$readPrompts ? 'checked' : ''}} />
                        <label for="checkbox-read-prompts" class="checkbox-sm steelblue" onclick="">@LANG('proj.Read Prompts')</label>
                    </div>
                </div>
                @endif
            @endif

            <hr/>
            <div class="submit-button mb-2">
                <button type="" onclick="$('#settings').toggle();" class="btn btn-primary btn-sm">@LANG('ui.Close')</button>
            </div>

		</div>
	</div>

    <section class="main-controls">
        <canvas id="feedback" class="visualizer" height="40px"></canvas>
    </section>

    <section class="sound-clips">
    </section>

	<!--------------------------------------------------------------->
	<!-- Bottom panel ----------------------------------------------->
	<!--------------------------------------------------------------->
	<div id="bottom-panel" class="btn-panel-bottom m-0 pb-1">
		<div class="glyphReaderMove mr-4"><a onclick="event.preventDefault(); prev()" href=""><span class="glyphicon glyphicon-backward"></span></a></div>
		<div id="buttonRecordGlyph" class="glyphReaderPlay mr-3"><a onclick="event.preventDefault(); startRecording()" href=""><span class="glyphicon glyphicon-record"></span></a></div>
		<div id="resume" class="glyphReaderPlay mr-3"><a onclick="event.preventDefault(); resume()" href=""><span class="glyphicon glyphcircle glyphicon-play"></span></a></div>
		<div id="pause"  class="glyphReaderPlay mr-3 mb-1"><a onclick="event.preventDefault(); pause()" href=""><span class="glyphicon glyphicon-pause"></span></a></div>
		<div id="readPage" class="glyphReaderPlay mr-3"><a onclick="event.preventDefault(); readPage('', '#slideDescription')" href=""><span class="glyphicon glyphicon-play-circle"></span></a></div>
		<div class="glyphReaderMove"><a onclick="event.preventDefault(); next()" href=""><span class="glyphicon glyphicon-forward"></span></a></div>
	</div>


</div><!-- container -->

@endsection

