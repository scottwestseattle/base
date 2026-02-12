<!--------------------------------------------------------------------------------------->
<!-- Language -->
<!--------------------------------------------------------------------------------------->
<div class="bright-blue pt-2">
    <div class="container page-normal">
        <span class='mini-menu'>
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
        </span>
        <div style="clear: both;"></div>
    </div>
</div>


