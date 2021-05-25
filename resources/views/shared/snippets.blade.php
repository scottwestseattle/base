@php
    $showLanguages = (isset($options['showLanguages'])) ? $options['showLanguages'] : false;
@endphp

<!-------------------------------------------------------->
<!-- Add misc data needed by the JS during runtime -->
<!-------------------------------------------------------->
<div class="data-misc"
	data-count="1"
	data-touchpath=""
	data-max="1"
	data-language={{$options['languageCodes']['short']}}
	data-language-long={{$options['languageCodes']['long']}}
	data-type="1"
	data-contenttype="frontpage"
	data-contentid="1"
	data-isadmin="0"
	data-userid="0"
	data-readlocation="0"
	data-useKeyboard="0"
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
<!-- The record form -->
<!--------------------------------------------------------------------------------------->
<div class="record-form text-center mt-2 p-1">

	<form method="POST" action="/definitions/create-snippet">
        <h3 class="practice-title mt-0 pt-0">@LANG('proj.Practice Speaking')</h3>
		<div class="">
		    <div style="xmin-height: 300px; ">
            <textarea
                id="textEdit"
                name="textEdit"
                class="form-control textarea-control"
                placeholder="{{__('proj.Enter text to read')}}"
                rows="7"
                style="font-size:18px;"
            >{{isset($options['snippet']) ? $options['snippet']->examples : ''}}</textarea>
            </div>
        </div>

        <span class='mini-menu'>
            @if ($showLanguages)
                @component('components.control-dropdown-language', [
                    'record' => isset($options['snippet']) ? $options['snippet'] : null,
                    'options' => $options['snippetLanguages'],
                    'selected_option' => $options['language'],
                    'field_name' => 'language_flag',
                    'select_class' => 'mini-border mt-1 mr-2',
                ])@endcomponent
                <select class="mini-border" onchange="changeVoice();" name="selectVoice" id="selectVoice"></select>
            @endif
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

<!--------------------------------------------------------------------------------------->
<!-- SNIPPETS -->
<!--------------------------------------------------------------------------------------->
@if (isset($options['records']) && count($options['records']) > 0)
    <h3 class="mt-2">@LANG('proj.Practice Text') <span style="font-size:.8em;">({{count($options['records'])}})&nbsp;&nbsp;
         @component('components.icon-read', ['href' => "/snippets/read", 'float' => 'inline-block'])@endcomponent
    </span></h3>
    <div class="text-center mt-2" style="">
        <div style="display: inline-block; width:100%">
            <table style="width:100%;">
            <?php $count = 0; ?>

            @if (isset($options['records']))
            @foreach($options['records'] as $record)

            <tr class="drop-box-ghost-small" style="vertical-align:middle;">
                <td style="color:default; text-align:left; padding:5px 10px;">
                    <table>
                    <tbody>
                        <tr>
                            <td style="padding-bottom:5px; font-size: 14px; font-weight:normal;">
                                <a href="" onclick="copyToReader(event, '{{$record->id}}', '#textEdit', '.record-form');">{{Str::limit($record->examples, 200)}}</a>
                                <input id="{{$record->id}}" type="hidden" value="{{$record->examples}}" />
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:.8em; font-weight:100;">
                                <div class="float-left mr-3">
                                    <img width="25" src="/img/flags/{{getSpeechLanguageShort($record->language_flag)}}.png" />
                                </div>
                                <div class="float-left" style="margin-top:2px; margin-right: 10px;">
                                    <div class=""><a href="/definitions/stats/{{$record->id}}">{{str_word_count($record->examples)}} {{trans_choice('ui.Word', 2)}}</a></div>
                                </div>
                                <div style="float:left;">
                                    @if (App\User::isAdmin())
                                    <div style="margin-right:5px; float:left;"><a href='/definitions/edit/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-edit"></span></a></div>
                                    <div style="margin-right:0px; float:left;"><a href='/definitions/delete/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-trash"></span></a></div>
                                    @endif
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
                <div class="mb-4"><a class="btn btn-sm btn-success" role="button" href="/practice">@LANG('ui.Show All')</a></div>
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
