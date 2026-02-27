<!--------------------------------------------------------------------------------------->
<!-- Language -->
<!--------------------------------------------------------------------------------------->
<div class="pt-2" style="background: linear-gradient(180deg, #f6da8f, #f7c257);">
    <div class="container page-normal">
        @if (!isLanguageCookieSet())
        <div class="pb-2">
            <h4>@LANG('proj.I want to learn'):</h4>
            <div class="m-1"><button type="button" class="btn btn-xl btn-light btn-language" onclick="setLanguageGlobal(0)"><table><tr><td style="width:30"><img height="40" src="/img/flags/en.png" class="mr-3" /></td><td>@LANG('geo.English')</td></tr></table></div>
            <div class="m-1"><button type="submit" class="btn btn-xl btn-light btn-language" onclick="setLanguageGlobal(1)"><table><tr><td style="width:30"><img height="40" src="/img/flags/es.png" class="mr-3" /></td><td>@LANG('geo.Spanish')</td></tr></table></div>
        </div>
        @else
        <div class='mini-menu'>
          <form method="" action="" autocomplete="off">
            <div style="display:flex; align-items: center;">
                <div class="float-left" style="">
                @if (true)
                    @component('components.control-dropdown-language', [
                        'options' => getLanguageOptions(isAdmin()),
                        'selected_option' => getLanguageId(),
                        'field_name' => 'language_flag',
                        'select_class' => 'mini-border mr-2',
                        'label' => __('ui.Learn') . ':',
                        'onchange' => 'setLanguageFromDropdown("#language_flag")',
                    ])@endcomponent
                @endif
                </div>
                @if (true || isset($loadReader) && $loadReader)
                <div class="" style="">
                    <label style="" for="selectVoice">Voices:</label>
                    <select class="mini-border xhidden" onchange="changeVoice();" name="selectVoice" id="selectVoice">
                        <option value="default">Default</option>
                    </select>
                </div>
                @endif
            </div>
          </form>
        </div>
        @endif
        <div style="clear: both;"></div>
    </div>
</div>

