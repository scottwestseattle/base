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
    $randomWords = isset($options['randomWords']) ? $options['randomWords'] : null;
    $newestWords = isset($options['newestWords']) ? $options['newestWords'] : null;
@endphp

<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!-- Page Header -->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->

@section('header')
<div style="width:100%; background-color: white; background-position: center; background-repeat: no-repeat; background-image:url('/img/spanish/load-loop.gif'); " >

<!--------------------------------------------------------------------------------------->
<!-- Banner Photo -->
<!--------------------------------------------------------------------------------------->
@if (false && isset($banner) )
    <div class="" style="background-image: url(/img/spanish/banners/{{$banner}}); background-size: 100%; background-repeat: no-repeat;">
        <a href="/"><img src="/img/spanish/{{App::getLocale()}}-spacer.png" style="width:100%;" /></a>
    </div>
@endif

<!--------------------------------------------------------------------------------------->
<!-- Logo and Subscribe Form-->
<!--------------------------------------------------------------------------------------->
@if (!Auth::check())
    <div class="" style="background-color:#4993FD">
        <div class="text-center py-3" >
            <img src="/img/logos/logo-{{domainName()}}.png" style="max-width:200px;"/>
            <form method="POST" action="/subscribe" class="px-3 mt-2">
                <div class="form-group text-center">
                    <div class="input-group mt-2" style="max-width:600px; margin:0 auto;">
                        <input name="email" id="email" type="email"
                            style="font-weight:200; font-size:18px;"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            maxlength="50"
                            placeholder="@LANG('ui.Email Address')"
                            required
                        />
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-success" type="button">@LANG('ui.Subscribe')</button>
                        </div>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mt-2 white small-thin-text">@LANG('base.Subscribe to mailing list')</div>

                </div>
                <div class="form-group">
                </div>
                {{ csrf_field() }}
            </form>
        </div>
    </div>
@endif

</div>
@endsection

<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!-- Page Body -->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->

@section('content')

<!--------------------------------------------------------------------------------------->
<!-- Dictionary, Lists, and Books shortcuts widget -->
<!--------------------------------------------------------------------------------------->
@if (isset($options['showWidgets']) && $options['showWidgets'])
    <div class="d-block d-md-none d-flex justify-content-center text-center bg-none mb-2">

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
		<div class="col-sm-12 col-lg-6 pb-2 px-3">
            <div class="card card-wotd truncate mt-1" style="">
                <div class="card-header card-header-potd">
                    <div>@LANG('proj.Word of the day')</div>
                    <div class="small-thin-text">@LANG('proj.A new word to learn every day')</div>
                </div>
                <div class="card-body card-body-potd">
                        <span id="wotdv">
                        <div>
                            @component('components.icon-read', ['color' => 'white', 'nodiv' => true, 'onclick' => "event.preventDefault(); readPage($('#wotd').val());"])@endcomponent
                            <b><span id="wotdTitle" class="ml-2">{{$wotd->title}}</span></b> - {{$wotd->translation_en}}
                        </div>
                        <div class="large-thin-text">
                            <div>
                                <i>Definición:</i> <span id="wotdDef">{{$wotd->definition}}</span>
                            </div>
                            <div>
                                <i>Ejemplo:</i><span id="wotdEx" class="mx-2">{{$wotd->examples}}</span>
                            </div>
                        </div>
                        </span>
                        <input type="hidden" id="wotd" value="{{$wotd->title . '. Definición: ' . $wotd->definition . '. Ejemplo: ' . $wotd->examples}}" />
                </div>
            </div>
		</div>
    @endif

    @if (isset($potd))
		<div class="col-sm-12 col-lg-6 px-3">
            <div class="card card-potd truncate mt-1">
                <div class="card-header card-header-potd">
                    <div>@LANG('proj.Phrase of the day')</div>
                    <div class="small-thin-text">@LANG('proj.Practice this phrase out loud')</div>
                </div>
                <div class="card-body card-body-potd">
                    <div class="xl-thin-text">
                        @component('components.icon-read', ['color' => 'white', 'nodiv' => true, 'onclick' => "event.preventDefault(); readPage($('#potd').val(), '#potdVisible')"])@endcomponent
                        <span id="potdVisible" class="slideDescription ml-2">{{$potd}}</span>
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

@php
    $showTabs = Auth::check() && isset($options['articlesPrivate']) && count($options['articlesPrivate']) > 0;
@endphp

@if ($showTabs)
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a id="nav-link-tab1" class="nav-link active" href="#" onclick="setTab(event, 1);">
            <span class="nav-link-tab">
                {{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{count($options['articlesPublic'])}})</span>
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a id="nav-link-tab2" class="nav-link" href="#" onclick="setTab(event, 2);">
            <span class="nav-link-tab">
                @LANG('ui.Private')&nbsp;<span style="font-size:.8em;">({{count($options['articlesPrivate'])}})</span>
            </span>
        </a>
    </li>
</ul>
@else
<h3 class="">
    {{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{count($options['articlesPublic'])}})</span>
</h3>
@endif

<div style="" id="tab-tab1">
    @component('shared.articles', ['records' => $options['articlesPublic'], 'release' => 'public'])@endcomponent
</div>
@if ($showTabs)
<div style="display:none" id="tab-tab2">
    @component('shared.articles', ['records' => $options['articlesPrivate'], 'release' => 'private'])@endcomponent
</div>
@endif

<!--------------------------------------------------------------------------------------->
<!-- NEWEST WORDS -->
<!--------------------------------------------------------------------------------------->

@if (isset($newestWords))
<h3>{{__('proj.Newest Words')}} ({{count($newestWords)}})</h3>
<div class="text-center mt-2" style="">
    <div style="display: inline-block; width:100%">
        <table style="width:100%;">
        @foreach($newestWords as $record)
        <tr class="drop-box-ghost-small" style="vertical-align:middle;">
            <td style="color:default; text-align:left; padding:5px 10px;">
                <table>
                <tbody>
                    <tr>
                        <td style="padding-bottom:5px; font-size: 14px; font-weight:normal;">
                            <a href="/definitions/view/{{$record->permalink}}">{{$record->title}}</a>
                            @if (isset($record->definition))
                                <div><span class="medium-thin-text"><i>{{trans_choice('proj.Definition', 1)}}:</i></span> {{getSentences($record->definition, 1)}}</div>
                            @endif
                            @if (isset($record->translation_en))
                                <div><span class="medium-thin-text"><i>{{__('base.English')}}:</i></span> {{$record->translation_en}}</div>
                            @endif
                            @if (isset($record->examples))
                                <div><span class="medium-thin-text"><i>{{trans_choice('proj.Example', 1)}}:</i></span> {{getSentences($record->examples, 1)}}</div>
                            @endif
                        </td>
                    </tr>
                </tbody>
                </table>
            </td>
        </tr>
        <tr style="" class=""><td colspan="2"><div style="height:15px;">&nbsp;</div></td></tr>
        @endforeach
        </table>
        <div class="mb-4"><a class="btn btn-sm btn-success" role="button" href="/dictionary">@LANG('proj.Dictionary')</a></div>
    </div>
</div>
@endif

@endsection
