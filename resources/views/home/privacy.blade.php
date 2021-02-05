@extends('layouts.app')

@section('content')

<!--------------------------------------------------------------------------------------->
<!-- Privacy Policy -->
<!--------------------------------------------------------------------------------------->

<div class="container page-normal">

	<h1>@LANG('ui.Privacy Policy')</h1>

	<p>@LANG('fp.privacy-p1') <a href="{{lurl('terms')}}">@LANG('ui.Terms of Use')</a>.</p>

	<p>@LANG('fp.privacy-p2')</p>

	<h3>@LANG('fp.privacy-h1')</h3>

	<p>@LANG('fp.privacy-p3')</p>

	<p>@LANG('fp.privacy-p4')</p>

	<p>@LANG('fp.privacy-p5')</p>

	<h3>@LANG('fp.privacy-h2')</h3>

	<p>@LANG('fp.privacy-p6')</p>

	<p>@LANG('fp.privacy-p7')</p>

	<h3>@LANG('fp.privacy-h3')</h3>

	<p>@LANG('fp.privacy-p8')</p>

	<h3>@LANG('fp.privacy-h4')</h3>

	<p>@LANG('fp.privacy-p9') <a href="{{lurl('contact')}}">@LANG('ui.by clicking here')</a>.</p>

</div>

@endsection
