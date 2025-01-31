<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!-- *** MAIN FRONTPAGE FOR LANGUAGE SITES *** -->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
@php
    $locale = app()->getLocale();
    $showGlobalSearchBox = false;
    $banner = isset($options['banner']) ? $options['banner'] : null;
    $wotd = isset($options['wotd']) ? $options['wotd'] : null;
    $potd = isset($options['potd']) ? $options['potd'] : null;
    $aotd = isset($options['aotd']) ? $options['aotd'] : null;
    $randomWords = isset($options['randomWords']) ? $options['randomWords'] : null;
    $newestWords = isset($options['newestWords']) ? $options['newestWords'] : null;
    $articleText = null;
    if (isset($aotd))
        $articleText = (Auth::check()) ? trunc($aotd->description, 300) : $aotd->description;
    //dump($options);

    $showWidgets = isset($options['showWidgets']) && $options['showWidgets'];
    $showWidgets = true;
@endphp

@extends('layouts.app')
@section('title', __(isset($options['title']) ? $options['title'] : 'base.Site Title') )
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!-- Page Header -->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->

@section('header')

@if (false)
<div style="width:100%; background-color: white; background-position: center; background-repeat: no-repeat; background-image:url('/img/spanish/load-loop.gif'); " >
@else
<div style="width:100%; background-color: white;">
@endif
<!--------------------------------------------------------------------------------------->
<!-- Sales Banner for Guests only -->
<!--------------------------------------------------------------------------------------->
@guest
    @if (false && \App\Site::hasOption('fpheader') && (!isLanguageCookieSet() /* || !isUserLevelCookieSet() */))
        <div class="fpbox-header">
            <div class="row row-course">
                @if (!isLanguageCookieSet())
                <div class="col-xs-12 col-md-6 fpSelectorRight mb-3">
                    <!-- h3>@LANG('proj.You are practicing '){{getLanguageName()}}</h3 -->
                    <h4>@LANG('proj.I want to practice:')</h4>
                    <div class="m-1"><button type="button" class="btn btn-xl btn-light btn-language" onclick="setLanguageGlobal(0)"><table><tr><td style="width:30"><img height="40" src="/img/flags/en.png" class="mr-3" /></td><td>@LANG('geo.English')</td></tr></table></a></div>
                    <div class="m-1"><button type="submit" class="btn btn-xl btn-light btn-language" onclick="setLanguageGlobal(1)"><table><tr><td style="width:30"><img height="40" src="/img/flags/es.png" class="mr-3" /></td><td>@LANG('geo.Spanish')</td></tr></table></a></div>
                    <div class="m-1"><button type="submit" class="btn btn-xl btn-light btn-language" onclick="setLanguageGlobal(3)"><table><tr><td style="width:30"><img height="40" src="/img/flags/it.png" class="mr-3" /></td><td>@LANG('geo.Italian')</td></tr></table></a></div>
                </div>
                @endif
                @if (false && !isUserLevelCookieSet())
                <div class="col-sm-12 col-md-6 fpSelectorLeft">
                    <!-- h3>@LANG('proj.Your level is  '){{getLanguageName()}}</h3 -->
                    <div class="">
                    <h4>@LANG('proj.My level is'):</h4>
                    <div class="m-1"><button type="button" class="btn btn-xl btn-light btn-language" onclick="setUserLevel({{LEVEL_A1}})"><table class="text-left" style="width:100%;"><tr><td class="level-number"><span class="fn">A1</span></td><td>@LANG('proj.Beginner')</td></tr></table></a></div>
                    <div class="m-1"><button type="button" class="btn btn-xl btn-light btn-language" onclick="setUserLevel({{LEVEL_A2}})"><table class="text-left" style="width:100%;"><tr><td class="level-number"><span class="fn">A2</span></td><td>@LANG('proj.Beginner')</td></tr></table></a></div>
                    <div class="m-1"><button type="button" class="btn btn-xl btn-light btn-language" onclick="setUserLevel({{LEVEL_B1}})"><table class="text-left" style="width:100%;"><tr><td class="level-number"><span class="fn">B1</span></td><td>@LANG('proj.Intermediate')</td></tr></table></a></div>
                    <div class="m-1"><button type="button" class="btn btn-xl btn-light btn-language" onclick="setUserLevel({{LEVEL_B2}})"><table class="text-left" style="width:100%;"><tr><td class="level-number"><span class="fn">B2</span></td><td>@LANG('proj.Intermediate')</td></tr></table></a></div>
                    <div class="m-1"><button type="button" class="btn btn-xl btn-light btn-language" onclick="setUserLevel({{LEVEL_C1}})"><table class="text-left" style="width:100%;"><tr><td class="level-number"><span class="fn">C1</span></td><td>@LANG('proj.Advanced')</td></tr></table></a></div>
                    <div class="m-1"><button type="button" class="btn btn-xl btn-light btn-language" onclick="setUserLevel({{LEVEL_C2}})"><table class="text-left" style="width:100%;"><tr><td class="level-number"><span class="fn">C2</span></td><td>@LANG('proj.Advanced')</td></tr></table></a></div>
                    </div>
                </div>
                @endif
            </div>
            @if (\App\Site::hasOption('fpsteps'))
            <button type="submit" class="btn btn-success" type="button">@LANG('ui.More Information')</button>
            @endif
        </div>

        @if (\App\Site::hasOption('fpsteps'))
        <!-- Fancy Step Boxes -->
        <div class="fpbox-container">
            <div class="fpbox">
                <h2>Step 1</h2>
                <h3>Online Classes</h3>
                <p>Attend up to 5 classes per week for daily practice and evaluation.</p>
            </div>
            <div class="fpbox">
                <h2>Step 2</h2>
                <h3>Teacher Feedback</h3>
                <p>Get detailed feedback after each class from the teacher in quiz format.</p>
            </div>
            <div class="fpbox">
                <h2>Step 3</h2>
                <h3>Online Tools</h3>
                <p>Use our powerful tools to memorize and implement corrections.</p>
            </div>
            <div class="fpbox">
                <h2>Step 4</h2>
                <h3>Master English</h3>
                <p>Master English through daily practice and repetition for a low monthly fee.</p>
            </div>
        </div>
        @endif

    @endif

    @if (false)
    <div class="bg-mars white text-center" style="min-height:300px;
        max-height:800px;
        background-image: url(/img/spacer.png);
        background-repeat: no-repeat;
        background-size:cover;
        background-position:top;
        ">
        @if (false)
        <h1 class="h1-landing-page pt-5">{{domainName()}}</h1>
        <h2 class="h2-landing-page">Take your Second Language to the next level</h2>
        @endif
    </div>
    @endif
@endguest

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
@if (false && !Auth::check())
    <div class="bg-purple text-center py-2 px-3" style="">
        <form method="POST" action="/subscribe" class="mt-2">
            <div class="form-group text-center mb-1">
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
                <div class="mt-2 mb-0 white small-thin-text">@LANG('base.Subscribe to mailing list')</div>
            </div>
            {{ csrf_field() }}
        </form>
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
<!-- Flag Language selector -->
<!--------------------------------------------------------------------------------------->
@if (isset($options['showLanguageFlags']) && $options['showLanguageFlags'])
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
<!-- Big Shortcuts widget -->
<!--------------------------------------------------------------------------------------->
@if ($showWidgets)
    <div class="d-block d-lg-none d-flex justify-content-center text-center bg-none mb-2 mt-0" style="sbwxmax-width: 500px;">
@php
    $style = 'width: 20%; max-width:90px;';
@endphp
        <div class="" style="{{$style}}">
            <a class="purple" href="{{route('practice', ['locale' => $locale])}}">
                <div class="glyphicon glyphicon-blackboard" style="font-size:35px;"></div>
                <div class="" style="font-size:10px;">{{__('proj.Practice')}}</div>
            </a>
        </div>

        <div class="" style="{{$style}}">
            <a class="purple" href="{{route('articles', ['locale' => $locale])}}">
                <div class="glyphicon glyphicon-globe" style="font-size:35px;"></div>
                <div class="" style="font-size:10px;">{{trans_choice('proj.Article', 2)}}</div>
            </a>
        </div>

        @if (\App\Site::hasOption('books'))
        <div class="" style="{{$style}}">
            <a class="purple" href="{{route('books', ['locale' => $locale])}}">
                <div class="glyphicon glyphicon-book" style="font-size:35px;"></div>
                <div class="" style="font-size:10px;">{{trans_choice('proj.Book', 2)}}</div>
            </a>
        </div>
        @else
        <div class="" style="{{$style}}">
            <a class="purple" href="{{route('favorites', ['locale' => $locale])}}">
                <div class="glyphicon glyphicon-heart" style="font-size:35px;"></div>
                <div class="" style="font-size:10px;">{{trans_choice('ui.Favorite', 2)}}</div>
            </a>
        </div>
        @endif

        <div class="" style="{{$style}}">
            <a class="purple" href="{{route('dictionary', ['locale' => $locale])}}">
                <div class="glyphicon glyphicon-text-background" style="font-size:35px;"></div>
                <div class="" style="font-size:10px;">{{__('proj.Dictionary')}}</div>
            </a>
        </div>

        @if (isAdmin() || \App\Site::hasOption('courses'))
            <div class="" style="{{$style}}">
                <a class="purple" href="{{route('courses', ['locale' => $locale])}}">
                    <div class="glyphicon glyphicon-education" style="font-size:35px;"></div>
                    <div class="" style="font-size:10px;">{{trans_choice('proj.Course', 2)}}</div>
                </a>
            </div>
        @else
            @if (Auth::check())
            <div class="" style="{{$style}}">
                <a class="purple" href="{{route('logout', ['locale' => $locale])}}">
                    <div class="glyphicon glyphicon-log-out" style="font-size:35px;"></div>
                    <div class="" style="font-size:10px;">{{__('ui.Log-out')}}</div>
                </a>
            </div>
            @else
            <div class="" style="{{$style}}">
                <a class="purple" href="{{route('login', ['locale' => $locale])}}">
                    <div class="glyphicon glyphicon-user" style="font-size:35px;"></div>
                    <div class="" style="font-size:10px;">{{__('ui.Login')}}</div>
                </a>
            </div>
            @endif
        @endif

    </div>
@endif

<!--------------------------------------------------------------------------------------->
<!-- ARTICLE OF THE DAY -->
<!--------------------------------------------------------------------------------------->
@if (\App\Site::hasOption('fpShowOtd') && isset($aotd) && !Auth::check())
	<div class="row row-course">
		<div class="col-12 pb-2 px-3">
            <div class="card card-aotd truncate mt-1" style="">

                @if (Auth::check())
                <div class="card-header card-header-potd">
                    <div>@LANG('proj.Article of the day')</div>
                    <div class="small-thin-text">{{App\DateTimeEx::getShortDateTime($aotd->display_date, 'M d, Y')}}</div>
                </div>
                <div class="card-body card-body-potd">
                    <div>
                        @component('components.icon-read', ['color' => 'white', 'nodiv' => true, 'onclick' => "event.preventDefault(); readPage($('#aotd').val(), '#aotdVisible');"])@endcomponent
                        <b><a id="" class="ml-2 link-white" href="/articles/view/{{$aotd->permalink}}">{{$aotd->title}}</a></b>
                        <p class="thin-text-18"><span id="aotdVisible">{{$articleText}}</span>...<a href="/articles/read/{{$aotd->id}}" class="link-white"><b>(@LANG('proj.Read All'))</b></a></p>
                        <input type="hidden" id="aotd" value="{{$articleText}}" />
                    </div>
                </div>

                @else

                <div class="card-header card-header-potd">
                    <div>@LANG('proj.How to use this web site')</div>
                    <div class="small-thin-text">{{date('M d, Y')}}</div>
                 </div>
                <div class="card-body card-body-potd">
                    <div>
                        @component('components.icon-read', ['color' => 'white', 'nodiv' => true, 'onclick' => "event.preventDefault(); readPage($('#aotd').val(), '#aotdVisible');"])@endcomponent
                        <b><a id="" class="ml-2 link-white" href="" onclick="event.preventDefault(); readPage($('#aotd').val(), '#aotdVisible');">{{$aotd->title}}</a></b>
                        <p class="thin-text-18"><span id="aotdVisible">{{$articleText}}</span></p>
                        <input type="hidden" id="aotd" value="{{$articleText}}" />
                    </div>
                </div>

                @endif

            </div>
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
<!-- LIST OF THINGS TO DO DAILY -->
<!--------------------------------------------------------------------------------------->
@component('shared.todo', ['options' => $options])@endcomponent

@if (false && isset($options['todo']))
    @php
    @endphp

    <div class="card mt-1 mb-3" style="">
        <div class="card-header">
            <div class="medium-thin-text">@LANG('proj.Daily Practice'): {{App\DateTimeEx::getShortDateTime(null, 'M d')}}</div>
        </div>
        <div class="card-body">

            <div style="display: inline-block; width:100%">
                <table style="width:100%;">
                @foreach($options['todo'] as $record)
                    <tr class="" style="vertical-align:middle;">
                        <td style="color:default; text-align:left; padding:5px 10px;">
                            <table>
                            <tbody>
                                <tr>
                                    <td style="padding-bottom:5px; font-size: 14px; font-weight:normal;">
                                        <div class="medium-thin-text">{{$record['action']}}</div>
                                        <div class=""><a href="{{$record['linkUrl']}}">{{$record['linkTitle']}}</a></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:.8em; font-weight:100;">
                                    </td>
                                </tr>
                            </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
    </div>
@endif

<!--------------------------------------------------------------------------------------->
<!-- ARTICLES -->
<!--------------------------------------------------------------------------------------->
@php
    $showPrivate = Auth::check() && isset($options['articlesPrivate']) && count($options['articlesPrivate']) > 0;
    $showOther = isAdmin() && isset($options['articlesOther']) && count($options['articlesOther']) > 0;
@endphp

@if ($showPrivate || $showOther)
<ul class="nav nav-tabs">

    <li class="nav-item">
        <a id="nav-link-tab1" class="nav-link active" href="#" onclick="setTab(event, 1);">
            <span class="nav-link-tab">
                {{trans_choice('proj.Article', 2)}}&nbsp;<span style="font-size:.8em;">({{count($options['articlesPublic'])}})</span>
            </span>
        </a>
    </li>

    @if ($showPrivate)
    <li class="nav-item">
        <a id="nav-link-tab2" class="nav-link" href="#" onclick="setTab(event, 2);">
            <span class="nav-link-tab">
                @LANG('ui.Private')&nbsp;<span style="font-size:.8em;">({{count($options['articlesPrivate'])}})</span>
            </span>
        </a>
    </li>
    @endif

    @if ($showOther)
    <li class="nav-item">
        <a id="nav-link-tab3" class="nav-link" href="#" onclick="setTab(event, 3);">
            <span class="nav-link-tab">
                @LANG('proj.Other')&nbsp;<span style="font-size:.8em;">({{count($options['articlesOther'])}})</span>
            </span>
        </a>
    </li>
    @endif
</ul>
@else
<h3 class="">
    <a href="/articles">{{trans_choice('proj.Article', 2)}}<span class="title-count">({{count($options['articlesPublic'])}})</span></a>
</h3>
@endif

<div style="" id="tab-tab1">
    @component('shared.stories', ['records' => $options['articlesPublic'], 'options' => $options, 'release' => 'public'])@endcomponent
</div>

@if ($showPrivate)
    <div style="display:none" id="tab-tab2">
        @component('shared.articles', ['records' => $options['articlesPrivate'], 'release' => 'private'])@endcomponent
    </div>
@endif

@if ($showOther)
    <div style="display:none" id="tab-tab3">
        @component('shared.articles', ['records' => $options['articlesOther'], 'release' => 'other'])@endcomponent
    </div>
@endif

<!--------------------------------------------------------------------------------------->
<!-- SNIPPETS - PRACTICE TEXT -->
<!--------------------------------------------------------------------------------------->
@if (false)
@component('shared.snippets', ['options' => $options, 'history' => $history])@endcomponent
@endif

<!--------------------------------------------------------------------------------------->
<!-- NEWEST WORDS -->
<!--------------------------------------------------------------------------------------->

@if (false && isset($newestWords))
<h3>{{__('proj.Newest Words')}}<span class="title-count">({{count($newestWords)}})</span></h3>
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
        <div class="mb-4"><a class="btn btn-sm btn-success" role="button" href="{{route('dictionary', ['locale' => $locale])}}">@LANG('proj.Dictionary')</a></div>
    </div>
</div>
@endif

@endsection
