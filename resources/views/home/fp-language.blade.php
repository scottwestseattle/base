@extends('layouts.app')
@section('title', __(isset($options['title']) ? $options['title'] : 'base.Site Title') )
@section('content')

<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!-- Front Page -->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->

<div class="container page-normal mt-1 bg-none">

@component('shared.snippets', ['options' => $options])@endcomponent

<!--------------------------------------------------------------------------------------->
<!-- ARTICLES -->
<!--------------------------------------------------------------------------------------->
@if (isset($options['articles']))
    <h3 class="mt-2">@LANG('view.Latest Articles') <span style="font-size:.8em;">({{count($options['articles'])}})</span></h3>
    <div class="text-center mt-2" style="">
        <div style="display: inline-block; width:100%">
            <table style="width:100%;">
            <?php $count = 0; ?>
            @foreach($options['articles'] as $record)

            <tr class="drop-box-ghost-small" style="vertical-align:middle;">
                <td style="color:default; text-align:left; padding:5px 10px;">
                    <table>
                    <tbody>
                        <tr><td style="padding-bottom:5px; font-size: 14px; font-weight:normal;"><a href="/entries/{{$record->permalink}}">{{$record->title}}</a></td></tr>
                        <tr>
                            <td style="font-size:.8em; font-weight:100;">
                                <div class="float-left mr-3">
                                    <img width="25" src="/img/flags/{{getSpeechLanguageShort($record->language_flag)}}.png" />
                                </div>
                                <div style="float:left;">
                                    @component('components.icon-read', ['href' => "/entries/read/$record->id", 'color' => 'white'])@endcomponent
                                    <div style="margin-right:15px; float:left;">{{$record->view_count}} @LANG('content.views')</div>
                                    <div style="margin-right:15px; margin-bottom:5px; float:left;"><a href="/entries/stats/{{$record->id}}">{{str_word_count($record->description)}} {{trans_choice('ui.Word', 2)}}</a></div>

                                    @if (App\User::isAdmin())
                                        <div style="margin-right:15px; float:left;">
                                            @component('components.control-button-publish', ['record' => $record, 'btnStyle' => 'btn-xxs', 'prefix' => 'entries', 'showPublic' => true])@endcomponent
                                        </div>
                                    @endif
                                </div>
                                <div style="float:left;">
                                    @if (App\User::isAdmin())
                                    <div style="margin-right:5px; float:left;"><a href='/entries/edit/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-edit"></span></a></div>
                                    <div style="margin-right:0px; float:left;"><a href='/entries/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-trash"></span></a></div>
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
            </table>
            <div class="mb-4"><a class="btn btn-sm btn-success" role="button" href="/articles">@LANG('ui.Show All')</a></div>
        </div>
    </div>
@endif

</div>

<!--------------------------------------------------------------------------------------->
<!-- BUY US A COFFEE BUTTON -->
<!--------------------------------------------------------------------------------------->
@if (isset($supportMessage))
<div class="text-center mb-4">
<script
    type="text/javascript"
    src="https://cdnjs.buymeacoffee.com/1.0.0/button.prod.min.js"
    data-name="bmc-button"
    data-slug="espdaily"
    data-color="#FFDD00"
    data-emoji=""
    data-font="Cookie"
    data-text="{{$supportMessage}}"
    data-outline-color="#000000"
    data-font-color="#000000"
    data-coffee-color="#ffffff" >
</script>
@endif
</div>

<!--------------------------------------------------------------------------------------->
<!-- PRE-FOOTER SECTION -->
<!--------------------------------------------------------------------------------------->
@if (isset($options['prefooter']))
<div class="mars-sky">
	<div class="container marketing text-center">
		<div class="pb-4 pt-3">
			<img src="/img/image5.png" style="max-width: 200px;" />
			@if (isset($randomWord))
				@component('components.random-word', ['record' => $randomWord])@endcomponent
			@else
				<h2 class="section-heading mt-0 mb-4">@LANG('fp.Frontpage Subfooter Title')</h2>
				<h4 style="font-size: 20px; font-weight: 400;">@LANG('fp.Frontpage Subfooter Body')</h4>
			@endif
		</div>
	</div>
</div>
@endif

@endsection

