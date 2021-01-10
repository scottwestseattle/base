@extends('layouts.app')

@section('content')

<!--------------------------------------------------------------------------------------->
<!-- Contact Page -->
<!--------------------------------------------------------------------------------------->

<div class="container page-normal">
	
	<h1>@LANG('ui.Contact')</h1>

	@if (false)
	<h3>@LANG('fp.Please contact us at the following email address.')</h3>
	
	<h3>{{$email}}</h3>
	@endif
			
</div>

@endsection
