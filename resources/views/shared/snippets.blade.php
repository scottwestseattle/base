@php
    $favoriteLists = (isset($options['favoriteLists'])) ? $options['favoriteLists'] : null;
    $showForm = (isset($options['showForm'])) ? $options['showForm'] : false;
    $count = (isset($options['count']) && $options['count'] > 0) ? $options['count'] : DEFAULT_LIST_LIMIT;
    $countNext = (isset($options['countNext']) && $options['countNext'] > 0) ? $options['countNext'] : $count;
    $countRead = (isset($options['countRead']) && $options['countRead'] > 0) ? $options['countRead'] : DEFAULT_LIST_LIMIT;
    $startNext = (isset($options['start'])) ? $options['start'] + $count : 0;
    $order = (isset($options['order'])) ? $options['order'] : 'desc';
    $autofocus = (isset($options['autofocus']) && $options['autofocus']) ? 'autofocus' : '';

    //
    // snippets
    //
    $snippets = isset($options['snippets']) ? $options['snippets'] : [];
    $hasSnippets = !empty($snippets) && count($snippets) > 0;
    $countPublic = $countPrivate = 0;
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
<!-- The Speech / Record form -->
<!--------------------------------------------------------------------------------------->
@if ($showForm)
<div class="record-form text-center p-1">

	<form method="POST" action="/definitions/create-snippet">
        <div class="">
            <button class="btn btn-xs btn-primary float-left mt-2" style="" onclick="event.preventDefault(); $('#textEdit').val(''); $('#textEdit').focus();" >{{__('ui.Add')}}</button>
        </div>
        <h3 class="practice-title mt-0 pt-0" style="" >@LANG('proj.Practice Speaking')</h3>
		<div class="">
		    <div style="">
            <textarea
                id="textEdit"
                name="textEdit"
                class="form-control textarea-control textEdit"
                placeholder="{{__('proj.Enter text to read')}}"
                rows="4"
                style="font-size:18px;"
            >{{isset($options['snippet']) ? $options['snippet']->title : ''}}</textarea>
            </div>
        </div>

        <span class='mini-menu'>
            <a type="button" class="btn btn-success btn-xs" href="" onclick="event.preventDefault(); $('#textEdit').val(''); $('#textEdit').focus();" class="ml-1">@LANG('ui.Clear')<a/>
            <a type="button" class="btn btn-success btn-xs" href="" onclick="copySnippet(event);" class="ml-1">@LANG('ui.Copy')<a/>
            <a type="button" class="btn btn-success btn-xs" href="" onclick="pasteSnippet(event);" class="ml-1">@LANG('ui.Paste')<a/>
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
@if ($hasSnippets)
    <h3 class="mt-2"><span class="float-left mr-2">@LANG('proj.Practice Text')</span>
        <span class="float-left mr-3" style="font-size:.7em; margin-top:6px;">({{count($snippets)}})</span>
        @component('components.icon-read', ['href' => "/snippets/read?count=$countRead&order=$order", 'float' => 'float-left'])@endcomponent
    </h3>
    <div style="clear:both;">
        <div class="medium-text" style="margin-bottom:3px;">
            <span class="mb-3"     style="vertical-align:bottom;"><a href="/snippets/review?action=flashcards&order={{$order}}">@LANG('proj.Flashcards')</a></span>
            <span class="ml-2" style="vertical-align:bottom;"><a href="/snippets/review?action=flashcards&count=20&order={{$order}}">@LANG('proj.Flashcards') (20)</a></span>
            <a class="ml-2 btn btn-success btn-xs" onclick="$('#filter-menu').toggle()" type="button">@LANG('ui.Sort')</a>
            <a id="publicButton" class="ml-2 btn btn-primary btn-xs" type="button" href="/practice/index?order=public&count={{$count}}">@LANG('ui.Public')</a>
            <div id="filter-menu" class="hidden mt-2">
                <span class=""     style="vertical-align:bottom;"><a href="/practice/index?order=asc&count=50">@LANG('ui.Oldest')</a></span>
                <span class="ml-2" style="vertical-align:bottom;"><a href="/practice/index?order=desc&count=50">@LANG('ui.Newest')</a></span>
                <span class="ml-2" style="vertical-align:bottom;"><a href="/practice/index?order=atoz&count=50">A-Z</a></span>
                <span class="ml-2" style="vertical-align:bottom;"><a href="/practice/index?order=ztoa&count=50">Z-A</a></span>
                @if (Auth::check())
                <span class="ml-2" style="vertical-align:bottom;"><a href="/practice/index?order=owner&count=50">@LANG('proj.My Text')</a></span>
                <span class="ml-2" style="vertical-align:bottom;"><a href="/practice/index?order=incomplete&count=50">@LANG('ui.Not Translated')</a></span>
                @endif
            </div>
        </div>
    </div>
        <div class="text-center mt-3" style="">
        <div style="display: inline-block; width:100%">
            <table style="width:100%;">
            @foreach($snippets as $record)
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

                    $record->isPublic() ? $countPublic++ : $countPrivate++;
                @endphp

                <tr style="" class=""><td colspan="2">
                @if ($countPrivate > 0 && $countPublic === 1)
                    <!-- put in a separator -->
                    <div class="large-thin-text ml-2 mt-2 mb-3 float-left" style="height:15px;">@LANG('ui.Public')</div>
                @else
                    <div style="height:10px;"></div>
                @endif
                    </td></tr>

                <tr class="drop-box-ghost-small" style="vertical-align:middle;">
                    <td style="color:default; text-align:left; padding:5px 10px;">
                        <table>
                        <tbody>
                            <tr>
                                <td style="padding-bottom:5px; font-size: 14px; font-weight:normal;">
                                @if ($showForm)
                                    <a href="" onclick="copyToReader(event, '{{$record->id}}', '#textEdit', '.record-form');">{{Str::limit($record->title, 200)}}</a>
                                    <input id="{{$record->id}}" type="hidden" value="{{$record->title}}" />
                                    <div class="small-thin-text">{{$record->translation_en}}</div>
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
                                            <div class="middle float-left"><a href='/definitions/edit/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-edit" style="color:{{$iconColor}}"></span></a></div>
                                    		<div class="middle float-left ml-2">@component('components.control-delete-glyph', ['svg' => 'trash-fill', 'style' => "color:$linkColor", 'href' => "/definitions/delete/$record->id", 'prompt' => 'ui.Confirm Delete'])@endcomponent</div>
                                            <div class="float-left">@component('gen.definitions.component-heart', ['record' => $record, 'id' => 1, 'lists' => $favoriteLists])@endcomponent</div>
                                        @endif

                                        @if (isAdmin())
                                            <div class="small-thin-text float-left ml-2">({{$owner}}) <a type="button" class="btn btn-primary btn-xs" href="/definitions/publish/{{$record->id}}">{{__($record->getReleaseStatusName())}}</a></div>
                                        @endif

                                        @if (false && Auth::check())
                                            <span class="ml-2 small-thin-text">{{trans_choice('ui.View', 2)}}: {{$record->view_count}}</span>
                                        @endif

                                    </div>

                                    @if (Auth::check())
                                        @component('gen.definitions.component-stat-badges', ['record' => $record, 'hideZeros' => true])@endcomponent
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
            </table>
            @if ($options['showAllButton'])
                <div class="my-3"><a class="btn btn-sm btn-success" role="button" href="/practice/index?order={{$order}}&start={{$startNext}}&count={{$countNext}}">@LANG('ui.Show More')</a></div>
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
    $('#textEdit').val('');

    // paste the selection: try the old way first...
    var succeed;
    try {
        succeed = document.execCommand("paste");
        console.log('execCommand: text pasted = ' + succeed);
    } catch(e) {
        succeed = false;
		console.log('execCommond: error copying text');
	}

	if (!succeed)
	{
        if (typeof navigator.clipboard.readText === "function")
        {
            navigator.clipboard
                .readText()
                .then(cliptext =>
                    $('#textEdit').val(cliptext),
                    err => console.log(err)
                );

    		console.log('navigator.clipboard.readText: pasted');
        }
        else
        {
    		console.log('navigator.clipboard.readText: not found');
        }
	}
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
