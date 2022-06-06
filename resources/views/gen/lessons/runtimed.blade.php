@extends('layouts.timedSlides')

@section('content')

<!-------------------------------------------------------->
<!-- Add misc data needed by the JS during runtime -->
<!-------------------------------------------------------->
<div class="data-misc"
	data-count="{{count($records)}}"
	data-type="{{$record->type_flag}}"
	data-touchpath="{{(isset($touchPath) ? $touchPath : '')}}"
	data-max="{{count($records)}}"
	data-bgalbum="{{$bgAlbum}}"
    @component('components.history-parameters', ['history' => $history])@endcomponent
></div>

	<!-------------------------------------------------------->
	<!-- Add the q and a records -->
	<!-------------------------------------------------------->
@foreach($records as $r)
	<div class="data-slides"
	    data-title="{{$r->title}}"
	    data-number="{{$r->section_number}}"
	    data-description="{{$r->description}}"
	    data-id="{{$r->id}}"
	    data-photo="{{$r->main_photo}}"
	    data-seconds="{{$r->getTime()['runSeconds']}}"
	    data-between="{{$r->getTime()['breakSeconds']}}"
	    data-countdown="{{isLocalhost() ? '1' : '10'}}"
	>
	</div>
@endforeach

	<!-------------------------------------------------------->
	<!-- Add the bg photos -->
	<!-------------------------------------------------------->
@foreach($bgs as $key => $value)
	<div class="data-bgs"
	    data-filename="{{$key}}"
	>
	</div>
@endforeach

<div class="container">

	<!-------------------------------------------------------->
	<!-- Header -->
	<!-------------------------------------------------------->
	<div style="margin-top: 5px;">

		<!-------------------------------------------------------->
		<!-- Top Row Buttons -->
		<!-------------------------------------------------------->
		<div style="margin: 0 5px 0 0;">
			<span style="font-size:1.3em; margin-right:10px;" class=""><a class="" role="" href="/{{$returnPath}}/{{$record->parent_id}}"><span class="glyphicon glyphicon-circle-arrow-up"></span></a></span>
			<span style="font-size:1.3em; margin-right:10px;" class=""><a onclick="event.preventDefault(); reload()" href=""><span id="button-repeat" class="glyphicon glyphicon-repeat"></span></a></span>
			<span style="font-size:1.3em; margin-right:10px;" class=""><a onclick="event.preventDefault(); pause()" href=""><span id="button-pause" class="glyphicon glyphicon-pause"></span></a></span>
			<span style="font-size:1.3em; margin-right:10px;" class=""><a onclick="event.preventDefault(); skip()" href=""><span id="button-skip" class="glyphicon glyphicon-step-forward"></span></a></span>
			<span style="font-size:1.3em; margin-right:10px;" class=""><a onclick="event.preventDefault(); mute()" href=""><span id="button-mute" class="glyphicon glyphicon-volume-up"></span></a></span>
		</div>

	</div>

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Start Panel - Index -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-start" class="slide-panel">

	    <div class="text-center">
            <p style="font-size:13px;" class="mb-0">
                <strong>{{$records[0]->course->title}}</strong>
                @if (App\User::isOwner($records[0]->course->user_id))
                <a href="/lessons/add/{{$record->parent_id}}" class="btn btn-success btn-xs ml-1" role="button">Add</a>
                @endif
            </p>
            <h3 class="mt-2 mb-2">{{isset($records[0]->title_chapter) ? $records[0]->title_chapter : 'Day ' . $record->lesson_number}}</h3>
            <p style="font-size:13px;" class="">{{count($records)}} exercises ({{$displayTime}})</p>
			@if (Auth::check())
				<p style="font-size:13px;"><a href="/history/add-public?type=2&programName={{urlencode($record->course->title)}}&programid={{$record->course->id}}&sessionName={{isset($record->title_chapter) ? urlencode($record->title_chapter) : urlencode('Day ' . $record->lesson_number)}}&seconds={{$record->id}}/{{$totalSeconds}}">Add to History</p>
			@endif
            <a onclick="event.preventDefault(); run()"  href="" class="btn btn-primary mb-3" role="button">Start</a>
        </div>

        <div class="card-deck">
        @foreach($records as $record)
        <div style="min-width:300px;" class="card mb-1">
            <div class="card-body">

                <table style="width:100%;">
                <tbody>
                    <tr>
                        <td style="width:100%;">
                                <div><a href="/lessons/view/{{$record->id}}">{{$record->section_number}}.&nbsp;{{$record->title}}</a></div>
                                <div>{{$record->getTime()['runSeconds']}} seconds ({{$record->getTime()['breakSeconds']}} rest)</div>
                                <div>
                                @if (App\User::isOwner($record->user_id))
                                    <div>
                                        <a href='/lessons/edit/{{$record->id}}'><span class="glyphCustom-sm glyphicon glyphicon-edit"></span></a>
                                        <a href='/lessons/confirmdelete/{{$record->id}}'><span class="glyphCustom-sm glyphicon glyphicon-delete"></span></a>
                                        @if ($record->isUnfinished())
                                        <a href='/lessons/publish/{{$record->id}}'><strong><span style="color:red;" class="glyphCustom-sm glyphicon glyphicon-flash"></span></strong></a>
                                        @endif
                                    </div>
                                @endif
                                <div style="font-size:13px;">{{$record->description}}</div>
                                </div>
                        </td>
                        <td>
                            <img width="100" src="/img/plancha/{{$record->main_photo}}" />
                        </td>
                    </tr>
                </tbody>
                </table>

            </div>
        </div>
        @endforeach
        </div><!-- card deck -->

	</div><!-- panel-start -->

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Countdown Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-countdown" class="slide-panel text-center">
	    <h1>Get Ready</h1>
        <div class="text-center"><h1 style="font-size:100px" class="showSeconds"></h1></div>
	    <h5>Coming up 1 of {{count($records)}}:</h5>
	    <div><img class="sliderPhoto" style="max-width:200px; width:70%;" src="/img/plancha/figure-plancha.png" /></div>
	    <h5 class="slideTitle"></h5>
	    <div class="slideDescription"></div>
	    <h5 class="slideSeconds mt-2"></h5>
	</div><!-- panel-countdown -->

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Run Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-run" class="slide-panel text-center">

    @if (true)
	    <div id="bg" class="text-center" style="
	        height:300px;
	        line-height:300px;
	        background-size: 500px;
	        background-repeat:no-repeat;
	        background-image:url('/* set randomly by js */');
	        background-position:center bottom;
	        "
	    >
    @else
	    <div id="bgTest" class="text-center" style="
	        height:300px;
	        line-height:300px;
	        background-size: 500px;
	        background-repeat:no-repeat;
	        background-image:url('/img/backgrounds/test2.jpg');
	        background-position:center bottom;
	        "
	    >
    @endif

	        <img class="sliderPhoto" style="vertical-align: bottom; max-width:400px; width:100%;" src="/* set by js */" />
	    </div>

	    <div class="slideCount"></div>
	    <h5 class="slideTitle"></h5>
	    <div class="slideDescription"></div>
	    <h5 class="slideSeconds mt-2"></h5>
        <div class="text-center"><h1 style="font-size:100px" class="showSeconds"></h1></div>
	</div><!-- panel-run -->

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Between Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-between" class="slide-panel text-center">
	    <h1>Take a Break</h1>
        <div class="text-center"><h1 style="font-size:100px" class="showSeconds"></h1></div>
	    <h5>Coming up next:</h5>
	    <div class="slideCount"></div>
	    <div><img class="sliderPhoto" style="max-width:200px; width:60%;" src="/img/plancha/figure-plancha.png" /></div>
	    <h5 class="slideTitle"></h5>
	    <div class="slideDescription"></div>
	    <h5 class="slideSeconds mt-2"></h5>
	</div><!-- panel-between -->

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- Pause Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-pause" class="slide-panel text-center">
	Pause Panel
	</div><!-- panel-pause -->

	<!---------------------------------------------------------------------------------------------------------------->
	<!-- End Panel -->
	<!---------------------------------------------------------------------------------------------------------------->
	<div id="panel-end" class="slide-panel ">
	    <div class="text-center">
            <h1>Congratulations!</h1>
            <h5 class="mb-3">{{count($records)}} Exercises completed in {{$displayTime}}</h5>
			<h5><a href="/history/add/{{urlencode($record->course->title)}}/{{$record->course->id}}/{{isset($record->title_chapter) ? urlencode($record->title_chapter) : urlencode('Day ' . $record->lesson_number)}}/{{$record->id}}/{{$totalSeconds}}">Add to History</h5>
	    </div>
        <div class="card-deck">
        @foreach($records as $record)
        <div style="min-width:300px;" class="card mb-1">
            <div class="card-body">

                <table style="width:100%;">
                <tbody>
                    <tr>
                        <td style="">
                                <div><a href="/lessons/view/{{$record->id}}">{{$record->section_number}}.&nbsp;{{$record->title}}</a></div>
                                <div>{{$record->getTime()['runTime']}}</div>
                                <div>
                                    @if (App\User::isOwner($record->user_id))
                                    <div>
                                        <a href='/lessons/edit/{{$record->id}}'><span class="glyphCustom-sm glyphicon glyphicon-edit"></span></a>
                                        <a href='/lessons/confirmdelete/{{$record->id}}'><span class="glyphCustom-sm glyphicon glyphicon-delete"></span></a>
                                    </div>
                                    @endif
                                </div>
                        </td>
                        <td style="width:10px;">
                                <img width="100" src="/img/plancha/{{$record->main_photo}}" />
                        </td>
                    </tr>
                </tbody>
                </table>

            </div>
        </div>
        @endforeach
        </div><!-- card deck -->


	</div><!-- panel-end -->

    <div class="text-center">
        <h1 style="font-size:100px" id="debug"></h1>
        <p id="bg-photo-name"></p>
    </div>

    <audio id="audio">
        <source src="" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

</div><!-- container -->

@endsection
