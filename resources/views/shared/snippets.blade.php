@php
    $favoriteLists = (isset($options['favoriteLists'])) ? $options['favoriteLists'] : null;
    $showForm = (isset($options['showForm'])) ? $options['showForm'] : false;

    $rpp = 50;
    $nextCount = (isset($options['count'])) ? $options['count'] : $rpp;
    $nextStart = (isset($options['start'])) ? $options['start'] + $rpp : 0;
    $sort = (isset($options['sort'])) ? $options['sort'] : '';
    $autofocus = (isset($options['autofocus']) && $options['autofocus']) ? 'autofocus' : '';
@endphp

<!-------------------------------------------------------->
<!-- Add misc data needed by the JS during runtime -->
<!-------------------------------------------------------->
<div class="data-misc"
	data-count="1"
	data-touchpath=""
	data-max="1"
	data-language="{{$options['languageCodes']['short']}}"
	data-language-long="{{$options['languageCodes']['long']}}"
	data-type="1"
	data-contenttype="frontpage"
	data-contentid="1"
	data-isadmin="0"
	data-userid="0"
	data-readlocation="0"
	data-useKeyboard="0"
    @component('components.history-parameters', ['history' => $history])@endcomponent
></div>

<!-------------------------------------------------------->
<!-- Add the body lines to read -->
<!-------------------------------------------------------->
<div class="data-slides"
    data-title="No title"
    data-number="1"
    data-description="@LANG('proj.Enter text to read')"
    data-id="0"
    data-seconds="10"
    data-between="2"
    data-countdown="1"
>
</div>

<!--------------------------------------------------------------------------------------->
<!-- The Search form -->
<!--------------------------------------------------------------------------------------->
@php
    $search = isset($search) ? $search : null;
    $records = isset($records) ? $records : [];
@endphp
<form method="POST" action="/dictionary/create-quick" autocomplete="off">

    <input value="" name="title" id="title" type="search"
        class="form-control form-control-sm form-control-inline py-2 border-right-0 border"
        placeholder="{{__('proj.Dictionary Search')}}"
        oninput="showSearchResult(this.value, false); $('#searchOptions').show()"
        {{$autofocus}}
    />

    <div id="searchOptions" class="mb-1 hidden">
        <table class="table-responsive table-condensed medium-text" style="">
            <thead>
                <tr>
                    <td>
                        <button id="" type="button" class="btn-info btn-xs"
                        onclick="showSearchResult($('#title').val(), true); $('#searchOptions').hide();"
                        >Search Articles/Books</button>

                        @if (false)
                        <input type="checkbox" name="search_articles" id="" class="mr-1"
                            style="display:inline;"
                            onclick="showSearchResult($('#title').val(), true); $('#searchOptions').hide();"
                        />
                    	<label for="search_articles" class="checkbox-label small-thin-text"
                            onclick="showSearchResult($('#title').val(), true); $('#searchOptions').hide();"
                    	>Search Articles/Books</label>
                    	@endif
                    </td>
                </tr>
            </thead>
        </table>
    </div>
    <div id="livesearch"></div>
    {{ csrf_field() }}
</form>

<!--------------------------------------------------------------------------------------->
<!-- The Speech / Record form -->
<!--------------------------------------------------------------------------------------->
@if ($showForm)
<div class="record-form text-center mt-2 p-1">

	<form method="POST" action="/definitions/create-snippet">
        <h3 class="practice-title mt-0 pt-0">@LANG('proj.Practice Speaking')</h3>
		<div class="">
		    <div style="">
            <textarea
                id="textEdit"
                name="textEdit"
                class="form-control textarea-control textEdit"
                placeholder="{{__('proj.Enter text to read')}}"
                rows="7"
                style="font-size:18px;"
            >{{isset($options['snippet']) ? $options['snippet']->title : ''}}</textarea>
            </div>
        </div>

        <span class='mini-menu'>
            <a href="" onclick="event.preventDefault(); $('#textEdit').val(''); $('#textEdit').focus();" class="ml-1">@LANG('ui.Clear')<a/>
            <a href="" onclick="copySnippet(event)" class="ml-1">@LANG('ui.Copy')<a/>
        </span>

        @if (!isMobile())
    	    @component('components.control-accent-chars-esp', ['labelClass' => 'white', 'visible' => true, 'target' => 'textEdit'])@endcomponent
        @endif

        <input type="hidden" name="returnUrl" value="{{$options['returnUrl']}}" />

		{{csrf_field()}}
    </form>

    <section class="main-controls">
        <canvas id="feedback" class="visualizer hidden" height="40px"></canvas>
        <div id="record-buttons">
            <button id="buttonRecord" class="btn-primary" onclick="event.preventDefault(); startRecording()">@LANG('ui.Record')</button>
            <button id="buttonRead" class="bg-purple" onClick="event.preventDefault(); readPage($('#textEdit').val())">@LANG('ui.Robot')</button>
            <button id="buttonSave" class="btn-success">@LANG('ui.Save')</button>
        </div>
    </section>

    <section class="sound-clips">
    </section>
</div>
@endif

<!--------------------------------------------------------------------------------------->
<!-- SNIPPETS -->
<!--------------------------------------------------------------------------------------->
@if (isset($options['records']) && count($options['records']) > 0)
    <h3 class="mt-2"><span class="float-left mr-2">@LANG('proj.Practice Text')</span>
        <span class="float-left mr-3" style="font-size:.7em; margin-top:6px;">({{count($options['records'])}})</span>
        @component('components.icon-read', ['href' => "/snippets/read", 'float' => 'float-left'])@endcomponent
    </h3>
    <div style="clear:both;">
        <div class="medium-text" style="margin-bottom:3px;">
            <span class="mb-3"     style="vertical-align:bottom;"><a href="/snippets/review/flashcards">@LANG('proj.Flashcards')</a></span>
            <span class="ml-2" style="vertical-align:bottom;"><a href="/snippets/review/flashcards/20">@LANG('proj.Flashcards') (20)</a></span>
            <a class="ml-2 btn btn-success btn-xs" onclick="$('#filter-menu').toggle()" type="button">Filter</a>
            <div id="filter-menu" class="hidden mt-2">
                <span class=""     style="vertical-align:bottom;"><a href="/practice/filter/parms?sort=asc&count=50">@LANG('ui.Oldest')</a></span>
                <span class="ml-2" style="vertical-align:bottom;"><a href="/practice/filter/parms?sort=desc&count=50">@LANG('ui.Newest')</a></span>
                <span class="ml-2" style="vertical-align:bottom;"><a href="/practice/filter/parms?sort=atoz&count=50">A-Z</a></span>
                <span class="ml-2" style="vertical-align:bottom;"><a href="/practice/filter/parms?sort=ztoa&count=50">Z-A</a></span>
                @if (Auth::check())
                <span class="ml-2" style="vertical-align:bottom;"><a href="/practice/filter/parms?sort=owner&count=50">@LANG('proj.My Text')</a></span>
                <span class="ml-2" style="vertical-align:bottom;"><a href="/practice/filter/parms?sort=incomplete&count=50">@LANG('ui.Not Translated')</a></span>
                @endif
            </div>
        </div>
    </div>
        <div class="text-center mt-3" style="">
        <div style="display: inline-block; width:100%">
            <table style="width:100%;">
            <?php $count = 0; ?>

            @if (isset($options['records']))
            @foreach($options['records'] as $record)

            @php
                $iconColor = 'default';
                $linkColor = 'purple';
                $isOwner = false;
                $owner = trans_choice('ui.Visitor', 1);
                if (isset($record->user_id) && intval($record->user_id) > 0)
                {
                    $member =  __('ui.Member') . ': ' . $record->user_id;

                    if (App\User::isOwner($record->user_id))
                    {
                        $owner = isAdmin() ? __('ui.Admin') : $member;
                        $isOwner = true;
                        $linkColor = 'red';
                    }
                    else
                    {
                        $owner = $member;
                        $iconColor = 'red';
                        $linkColor = 'red';
                    }
                }
            @endphp
            <tr class="drop-box-ghost-small" style="vertical-align:middle;">
                <td style="color:default; text-align:left; padding:5px 10px;">
                    <table>
                    <tbody>
                        <tr>
                            <td style="padding-bottom:5px; font-size: 14px; font-weight:normal;">
                            @if ($showForm)
                                <a href="" onclick="copyToReader(event, '{{$record->id}}', '#textEdit', '.record-form');">{{Str::limit($record->title, 200)}}</a>
                                <input id="{{$record->id}}" type="hidden" value="{{$record->title}}" />
                                @if (Str::startsWith($record->permalink, '-'))
                                    <div class="red">{{$record->permalink}}</div>
                                @endif
                            @else
                                <div class="">{{Str::limit($record->title, 200)}}@if (false) ({{$record->id}})@endif</div>
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:.8em; font-weight:100;">
                                <div class="float-left mr-3">
                                    <img width="25" src="/img/flags/{{getSpeechLanguageShort($record->language_flag)}}.png" />
                                </div>

                                @if (!isset($record->translation_en))
                                    @if (isAdmin() || $isOwner)
                                        <div class="float-left mr-3 pb-1">
                                            <a href="/definitions/edit/{{$record->id}}"><span style="color:{{$linkColor}};">@LANG('proj.Add Translation')</span></a>
                                        </div>
                                    @endif
                                @endif

                                <div style="float:left;">
                                    @if (isAdmin() || $isOwner)
                                        <div style="margin-right:5px; float:left;"><a href='/definitions/edit/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-edit" style="color:{{$iconColor}}"></span></a></div>
                                        <div style="margin-right:0px; float:left;"><a href='/definitions/delete/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-trash" style="color:{{$iconColor}}"></span></a></div>
                                        <div class="float-left">
                                            @component('gen.definitions.component-heart', ['record' => $record, 'id' => 1, 'lists' => $favoriteLists])@endcomponent
                                        </div>
                                    @endif

                                    @if (isAdmin())
                                        <div class="small-thin-text float-left ml-2">({{$owner}})</div>
                                    @endif

                                    <span class="ml-2 small-thin-text">{{trans_choice('ui.View', 2)}}: {{$record->view_count}}</span>

                                </div>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </td>
            </tr>

            <tr style="" class=""><td colspan="2"><div style="height:15px;">&nbsp;</div></td></tr>

            @endforeach
            @endif
            </table>
            @if ($options['showAllButton'])
                <div class="mb-4"><a class="btn btn-sm btn-success" role="button" href="/practice/index?sort={{$sort}}&start={{$nextStart}}&count={{$nextCount}}">@LANG('ui.Show More')</a></div>
            @endif
        </div>
    </div>
@endif

<script>

function saveSnippet(event)
{
    event.preventDefault();
}

function copySnippet(event)
{
    event.preventDefault();

    var txtarea = document.getElementById('textEdit');
    var start = txtarea.selectionStart;
    var finish = txtarea.selectionEnd;
    if (start != finish) // doesn't work
    {
        // already selected, use the current selection
        //console.log(start);
        //console.log(finish);
        txtarea.select(); // just select it all for now
    }
    else
    {
        txtarea.select();
    }

    // copy the selection
    var succeed;
    try {
        succeed = document.execCommand("copy");
        //console.log('text copied: ' + succeed);
    } catch(e) {
        succeed = false;
		//console.log('error copying text');
	}
}

function pasteSnippet(event)
{
    event.preventDefault();

    $('#textEdit').focus();
    document.execCommand("paste");
}

function toggleTextView()
{
    if ($('#textShow').is(':visible'))
    {
        setEdit();
    }
    else
    {
        setShow();
    }

}

function setEdit()
{
    return;

    //console.log('setEdit');
    //$('#buttonEdit').text('Show');
    //$('#textEdit').show();
    //$('#textShow').hide();
}

function setShow()
{
    return;

    //console.log('setShow');
    //$('#textShow').html($('#textEdit').val())
    //$('#buttonEdit').text('Edit');
    //$('#textEdit').hide();
    //$('#textShow').show();
}

</script>
