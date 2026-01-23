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
    $randomWords = isset($options['randomWords']) ? $options['randomWords'] : null;
    $newestWords = isset($options['newestWords']) ? $options['newestWords'] : null;
    $showWidgets = isset($options['showWidgets']) && $options['showWidgets'];
    $showWidgets = true;

    $articleText = null;
    $aotd = isset($options['aotd']) ? $options['aotd'] : null;
    //dump($aotd);
    if (isset($aotd))
    {
        if (isset($options['aotd']['sentences'][0]) && isset($options['aotd']['sentences-trx'][0]))
        {
            $articleText = $options['aotd']['sentences'][0];
            $articleTrx = $options['aotd']['sentences-trx'][0];

            if (isset($options['aotd']['sentences'][1]) && isset($options['aotd']['sentences-trx'][1]))
            {
                $articleText .= ' ' . $options['aotd']['sentences'][1];
                $articleTrx .= ' ' . $options['aotd']['sentences-trx'][1];
            }
        }
        elseif (isset($aotd->description))
        {
            $articleText = trunc($aotd->description, 200);
        }
        else
        {
            $articleText = 'no article.';
        }
    }
@endphp
@extends('layouts.app')
@section('title', __(isset($options['title']) ? $options['title'] : 'base.Site Title') )
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!-- Page Header -->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->

@section('content')

<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!-- Page Body -->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->

<!--------------------------------------------------------------------------------------->
<!-- STORY OF THE DAY -->
<!--------------------------------------------------------------------------------------->

@if ((\App\Site::hasOption('fpShowOtd') && isset($aotd)))
	<div class="row row-course">
		<div class="col-12 pb-2 px-3">
            <div class="truncate mt-1" style="">
                <div class="">
                    <h1>@LANG('proj.Story of the Day')</h1>
                    <div class="small-thin-text">{{date('M d, Y')}}</div>
                </div>
                <div class="">
                    <div>
                        <b><a id="" class="thin-text-18" style="font-size:2em; text-decoration: none;" href="{{route('articles.view', ['locale' => $locale, 'permalink' => $aotd->permalink])}}">{{$aotd->title}}</a></b>
                        <div id="aotdVisible" class="thin-text-18">
                            <div>{{$articleText}}</div>
                            <div class="mt-2" style="color: #137bf8;">{{$articleTrx}}</div>
                        </div>
                        <div class="mt-2"><a style="text-decoration:none; font-size:.9em; color: black" href="{{route('articles.view', ['locale' => $locale, 'permalink' => $aotd->permalink])}}">@LANG('proj.Read All')...</a></div>
                        <input type="hidden" id="aotd" value="{{$articleText}}" />
                    </div>
                </div>
            </div>
		</div>
	</div>
@endif

<!--------------------------------------------------------------------------------------->
<!-- SIDE-BY-SIDE STORY OF THE DAY -->
<!--------------------------------------------------------------------------------------->
@php
    $record = $aotd;
    //dd($options);
@endphp
@if (false && isset($aotd['sentences']))
<div style="clear: both;" class="">
    <!-- Title -->
    <h1 name="title">{{$record->title}}</h1>

    <!-- Summary -->
    @if (strlen(trim($record->description_short)) > 0)
        <div class="entry" style="margin-bottom:20px; font-size:1.3em;">
            <div><i>{{$record->description_short}}</i></div>
        </div>
    @endif

    <div class="entry-div" style="margin-top:20px; width:100%; font-size:1.1em;">
        <div class="entry" style="width:100%;">
            <span id="description" name="description" class="">{!! $record->description !!}</span>
            @if (isset($translation))
                <span id="translation" name="translation" class="hidden">
                    <table>
                        <tbody>
                            @foreach($options['sentences'] as $s)
                                <tr class="mb-3">
                                    <td class="pb-4 pr-4" style="vertical-align:top; width:50%;"><span class="mr-2 fn">{{$loop->index + 1}}</span>{{$s}}</td>
                                    @php $trx = isset($options['sentences_translation'][$loop->index]) ? $options['sentences_translation'][$loop->index] : null; @endphp
                                    <td class="pb-4" style="vertical-align:top;"><span class="mr-2 fn">{{$loop->index + 1}}</span>{{$trx}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </span>
            @endif
        </div>
    </div>
</div>
@endif

<!--------------------------------------------------------------------------------------->
<!-- SHOW ALL STORIES with covers -->
<!--------------------------------------------------------------------------------------->
@component('shared.stories', ['records' => $options['articlesPublic'], 'options' => $options, 'release' => 'public'])@endcomponent

@endsection
