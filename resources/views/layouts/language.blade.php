<!--------------------------------------------------------------------------------------->
<!-- Language -->
<!--------------------------------------------------------------------------------------->
<div class="bright-blue">
    <div class="container page-normal">
        <span class='mini-menu'>

          <form method="" action="" autocomplete="off">
            @if ($showGlobalSearchBox)
                <label>Search: </label>
                <input value="" name="searchText" id="searchText" type="search"
                    class="form-control-inline pl-1 border-right-0 border mini-border mr-2"
                    placeholder="{{__('proj.Dictionary Search')}}"
                    oninput="showSearchResult(this.value, false); $('#searchOptions').show()"
                    style="width:135px;"
                />
            @endif

            @component('components.control-dropdown-language', [
                'options' => getLanguageOptions(true),
                'selected_option' => getLanguageId(),
                'field_name' => 'language_flag',
                'select_class' => 'mini-border mt-2 mr-2',
                'label' => trans_choice('ui.Language', 1) . ':',
                'onchange' => 'setLanguageGlobal()',
            ])@endcomponent
            <select class="mini-border hidden" onchange="changeVoice();" name="selectVoice" id="selectVoice"></select>
          </form>
        </span>
    </div>
</div>

@if ($showGlobalSearchBox)
<div class="container page-normal" style="">
    <div id="livesearch"></div>
</div>
@endif

