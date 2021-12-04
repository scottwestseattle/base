@extends('layouts.app')
@section('title', __(isset($options['title']) ? $options['title'] : 'base.Site Title') )
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!-- Front Page -->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->

<!--------------------------------------------------------------------------------------->
<!-- Banner -->
<!--------------------------------------------------------------------------------------->

<!--------------------------------------------------------------------------------------->
<!-- Body Content -->
<!--------------------------------------------------------------------------------------->
@section('content')

<div class="container page-normal mt-1 bg-none">

@component('shared.snippets', ['options' => $options])@endcomponent

<!--------------------------------------------------------------------------------------->
<!-- ARTICLES -->
<!--------------------------------------------------------------------------------------->
@component('shared.articles', ['options' => $options])@endcomponent

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

@stop

