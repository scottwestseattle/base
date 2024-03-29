<!--------------------------------------------------------------------------------------->
<!-- Language -->
<!--------------------------------------------------------------------------------------->
<div class="bright-blue">
    <div class="container page-normal">
        <span class='mini-menu'>

          <form method="" action="" autocomplete="off">
            @component('components.control-dropdown-language', [
                'options' => getLanguageOptions(isAdmin()),
                'selected_option' => getLanguageId(),
                'field_name' => 'language_flag',
                'select_class' => 'mini-border mt-2 mr-2',
                'label' => __('proj.Practice') . ':',
                'onchange' => 'setLanguageFromDropdown("#language_flag")',
            ])@endcomponent
            <select class="mini-border hidden" onchange="changeVoice();" name="selectVoice" id="selectVoice"></select>
          </form>
        </span>
    </div>
</div>


