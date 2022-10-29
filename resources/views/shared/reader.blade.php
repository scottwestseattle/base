@extends('layouts.reader')
@section('title', __('Reading') . ' ' . $title )
@section('content')
@php
    $recordId = isset($recordId) ? $recordId : -1;
    $readLocation = isset($readLocation) ? $readLocation : null;
    $count = isset($options['count']) ? $options['count'] : count($lines['text']);
    $randomOrder = isset($options['randomOrder']) ? $options['randomOrder'] : false;
	$hasTranslation = isset($lines['translation']);// && count($lines['text']) == count($lines['translation']); // translation matches text
	$showTranslationControls = isset($lines['translation']); // show the translation in case it needs work
@endphp

<!-------------------------------------------------------->
<!-- Add misc data needed by the JS during runtime -->
<!-------------------------------------------------------->
<div class="data-misc"
    data-title="{{$title}}"
	data-count="{{$count}}"
	data-max="{{$count}}"
	data-touchpath="{{(isset($touchPath) ? $touchPath : '')}}"
	data-language="{{$languageCodes['short']}}"
	data-language-long="{{$languageCodes['long']}}"
	data-type="{{$contentType}}"
	data-contenttype="{{$contentType}}"
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
    @component('components.history-parameters', ['history' => $history])@endcomponent
></div>

	<!-------------------------------------------------------->
	<!-- Add the body lines to read -->
	<!-------------------------------------------------------->
@foreach($lines['text'] as $line)
	<div class="data-slides"
	    data-title="{{$line}}"
	    data-number="1"
	    data-description="{{$line}}"
	    data-translation="{{$hasTranslation ? $lines['translation'][$loop->index] : ''}}"
	    data-id="{{$recordId}}"
	    data-seconds="10"
	    data-between="2"
	    data-countdown="1"
	>
	</div>
@endforeach

<div class="container">
	<!-------------------------------------------------------->
	<!-- Header -->
	<!-------------------------------------------------------->
	<div class="text-center m-2" style="">

		<!-------------------------------------------------------->
		<!-- Top Row Buttons -->
		<!-------------------------------------------------------->
		<div>
			<span class="glyphReader">
            @if (false)
			    <a href="{{$options['return']}}"><span class="glyphicon glyphicon-circle-arrow-up"></span></a>
            @endif
			</span>

			<span class="glyphReader">
            @if (false)
			    <a href="{{$options['return']}}"><span class="glyphicon glyphicon-circle-arrow-up"></span></a>
            @endif
                <a href="{{$options['return']}}"><button type="button" class="btn btn-sm btn-primary mb-1"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-up"></span>{{trans_choice('ui.Return', 1)}}</button></button></a>
			</span>
			<span class="glyphReader"><a onclick="event.preventDefault(); reload()" href=""><span id="button-repeat" class="glyphicon glyphicon-repeat"></span></a></span>
			<span class="glyphReader"><a onclick="zoom(event, -3);" href=""><span class="glyphicon glyphicon-zoom-out"></span></a></span>
			<span class="glyphReader"><a onclick="zoom(event, 3);" href=""><span class="glyphicon glyphicon-zoom-in"></span></a></span>
			<span class="glyphReader"><a onclick="event.preventDefault(); $('#settings').toggle();" href=""><span class="glyphicon glyphicon-cog"></span></a></span>
			@if (false && Auth::check())
				<span class="glyphReader"><a onclick="toggleActiveTab(event, '#tab2', '#tab1', '.tab-body');" href=""><span class=""></span></a></span>
			@endif
			@if ($hasTranslation)
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
					<a onclick="incLine(event, -50)" href=""><span id="button-decrement-line" class="glyphicon glyphCustom glyphicon-minus-sign"></span></a>
					<div id="readCurrLine" class="middle large-text mb-2" style="min-width:85px;">{{trans_choice('ui.Line', 2)}} </div>
					<a onclick="incLine(event, 50)" href=""><span id="button-increment-line" class="glyphicon glyphCustom glyphicon-plus-sign"></span></a>

					<div id="elapsedTime" class="mt-5"></div>
					<div class="statusMsg" class="ml-3"></div>

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
						<span id="clock" class="ml-3">00:00</span>
						<span class="statusMsg ml-3">Wake not set</span>
					</div>

					<div id="debug"></div>
					<div id="slideDescription" class="slideDescription" style="font-weight:300; font-size: 18px;" onmouseup="getSelectedText(1);" ondblclick="getSelectedText(2);" ontouchend="getSelectedText();"></div>
					<div class="" style="color: green;" id="selected-word"></div>
					<div class="" style="color: green;" id="selected-word-definition"></div>
                    <div id="slideTranslation" class="mt-2 steelblue hidden" style="font-size: 17px;" ></div>
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
	<div id="settings" class="overlay" style="display:none;">
		<div class="text-center mt-3">
			<div class="mb-2">
                <label for="languages" class="checkbox-big-label">{{trans_choice('ui.Voice', 1)}}:</label>
                <div name="languages" id="languages" class="mt-1" style="display:default;">
                    <select class="" onchange="changeVoice();" name="selectVoice" id="selectVoice"></select>
                </div>
			</div>
            <hr />
            <div>
                <label for="read_flag" class="checkbox-big-label">@LANG('ui.Read'):</label>
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

            <div class="">
                <div>@LANG('proj.Seconds to pause between lines'):</div>
                <a onclick="inc(event, '#pause_seconds', -1)" href=""><span class="glyphicon glyphCustom glyphicon-minus-sign"></span></a>
                <div class="middle mb-2" style="min-width:30px;"><span class="ml-2 mr-2" style="font-size:25px;" id="pause_seconds">0</span></div>
                <a onclick="inc(event, '#pause_seconds', 1)" href=""><span class="glyphicon glyphCustom glyphicon-plus-sign"></span></a>
            </div>
            <hr />
            <div class="">
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
                        <input type="checkbox" name="checkbox-flip" id="checkbox-flip" style="height:20px; position:static;" />
                        <label for="checkbox-flip" class="checkbox-sm steelblue" onclick="">@LANG('proj.Reverse text and translation')</label>
                    </div>

                    <div class="mt-1 ml-1">
                        <input type="checkbox" name="checkbox-show" id="checkbox-show" onclick="$('#translation-show').toggle();" style="position: static;" />
                        <label for="checkbox-show" class="checkbox-sm steelblue">@LANG('proj.Show All Translations')</label>
                    </div>
                    <div class="mt-3 text-left">
                        <div id="translation-show" class="hidden">
                        <table><tbody>
                        @foreach($lines['text'] as $line)
                            <tr class="mb-3">
                                <td class="pb-4 pr-4" style="vertical-align:top;">{{$loop->index + 1}}) {{$line}}</td>
                                @php
                                    $trx = (isset($lines['translation'][$loop->index])) ? $lines['translation'][$loop->index] : '';
                                @endphp
                                <td class="pb-4" style="vertical-align:top;">{{$loop->index + 1}}) {{$trx}}</td>
                            </tr>
                        @endforeach
                        </tbody></table>
                        </div>
                    </div>
                </div>
                @endif
            @endif

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

