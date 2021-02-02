@extends('layouts.app')
@section('title', __(isset($options['title']) ? $options['title'] : 'base.Site Title') )

<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!-- Front Page -->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->

@php
    $banner = isset($options['banner']) ? $options['banner'] : null;
    $articles = isset($options['articles']) ? $options['articles'] : null;
    $wotd = isset($options['wotd']) ? $options['wotd'] : null;
    $potd = isset($options['potd']) ? $options['potd'] : null;
@endphp

<!--------------------------------------------------------------------------------------->
<!-- Banner Photo -->
<!--------------------------------------------------------------------------------------->
@if (isset($banner))
@section('banner')
<div style="width:100%; background-color: white; background-position: center; background-repeat: no-repeat; background-image:url('/img/spanish/load-loop.gif'); " >
    <div class="" style="background-image: url(/img/spanish/banners/{{$banner}}); background-size: 100%; background-repeat: no-repeat;">
        <a href="/"><img src="/img/spanish/{{App::getLocale()}}-spacer.png" style="width:100%;" /></a>
    </div>
</div>
@endsection
@endif

@section('content')

<!--------------------------------------------------------------------------------------->
<!-- WORD AND PHRASE OF THE DAY -->
<!--------------------------------------------------------------------------------------->
@if (isset($wotd) || isset($potd))
	<div class="row row-course">
    @if (isset($wotd))
		<div class="col-sm-12 col-lg-6 col-course" style="">
            <div class="card card-wotd truncate mt-1" style="">
                <div class="card-header card-header-potd">
                    <div>@LANG('proj.Word of the day')</div>
                    <div class="small-thin-text">@LANG('proj.A new word to learn every day')</div>
                </div>
                <div class="card-body card-body-potd">
                    @if(isset($wotd))
                        <div><b>{{$wotd->title}}</b> - <i>{{$wotd->translation_en}}</i></div>
                        <div class="large-thin-text">
                            {{$wotd->examples}}
                            @component('components.icon-read', ['color' => 'white', 'nodiv' => true, 'onclick' => "event.preventDefault(); readPage($('#wotd').val())"])@endcomponent
                        </div>

                        <input type="hidden" id="wotd" value="{{$wotd->title . '. ' . $wotd->examples}}" />
                    @else
                        <div>@LANG('ui.Not Found')</div>
                    @endif
                </div>
            </div>
		</div>
    @endif

    @if (isset($potd))
		<div class="col-sm-12 col-lg-6 col-course" style="">
            <div class="card card-potd truncate mt-1" style="">
                <div class="card-header card-header-potd">
                    <div>@LANG('proj.Phrase of the day')</div>
                    <div class="small-thin-text">@LANG('proj.Practice this phrase out loud')</div>
                </div>
                <div class="card-body card-body-potd">
                    <div class="xl-thin-text">
                        {{$potd}}
                        @component('components.icon-read', ['color' => 'white', 'nodiv' => true, 'onclick' => "event.preventDefault(); readPage($('#potd').val())"])@endcomponent
                    </div>
                    <input type="hidden" id="potd" value="{{$potd}}" />
                </div>
            </div>
		</div>
    @endif

	</div>

@endif

<!--------------------------------------------------------------------------------------->
<!-- SNIPPETS - PRACTICE TEXT -->
<!--------------------------------------------------------------------------------------->
@component('shared.snippets', ['options' => $options])@endcomponent

<!--------------------------------------------------------------------------------------->
<!-- ARTICLES -->
<!--------------------------------------------------------------------------------------->
@component('shared.articles', ['options' => $options])@endcomponent



@endsection
