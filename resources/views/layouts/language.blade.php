<!--------------------------------------------------------------------------------------->
<!-- Language -->
<!--------------------------------------------------------------------------------------->
<div class="bright-blue">
    <div class="container page-normal">
        <span class='mini-menu'>
            @component('components.control-dropdown-language', [
                'options' => getLanguageOptions(true),
                'selected_option' => getLanguageId(),
                'field_name' => 'language_flag',
                'select_class' => 'mini-border mt-1 mr-2',
                'label' => trans_choice('ui.Language', 1) . ':',
                'onchange' => 'setLanguageGlobal()',
            ])@endcomponent
            <select class="mini-border" onchange="changeVoice();" name="selectVoice" id="selectVoice"></select>
        </span>
    </div>
</div>
