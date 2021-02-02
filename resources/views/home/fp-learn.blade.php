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
<!-- Dictionary, Lists, and Books shortcuts widget -->
<!--------------------------------------------------------------------------------------->
@if (isset($options['showWidgets']) && $options['showWidgets'])
    <div class="hidden-xs mb-3"></div>
    <div class="d-block d-md-none d-flex justify-content-center text-center bg-none p-0 mt-3">

        <div class="" style="width: 25%;">
            <a class="purple" href="/articles">
                <div class="glyphicon glyphicon-globe" style="font-size:35px;"></div>
                <div class="" style="font-size:10px;">{{trans_choice('proj.Article', 2)}}</div>
            </a>
        </div>
        <div class="" style="width: 25%;">
            <a class="purple" href="/books">
                <div class="glyphicon glyphicon-book" style="font-size:35px;"></div>
                <div class="" style="font-size:10px;">{{trans_choice('proj.Book', 2)}}</div>
            </a>
        </div>
        <div class="" style="width: 25%;">
            <a class="purple" href="/dictionary">
                <div class="glyphicon glyphicon-font" style="font-size:35px;"></div>
                <div class="" style="font-size:10px;">{{__('proj.Dictionary')}}</div>
            </a>
        </div>
        <div class="" style="width: 25%;">
            <a class="purple" href="/favorites">
                <div class="glyphicon glyphicon-th-list" style="font-size:35px;"></div>
                <div class="" style="font-size:10px;">{{trans_choice('ui.List', 2)}}</div>
            </a>
        </div>

    </div>
@endif

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
