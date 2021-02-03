@extends('layouts.app')
@section('title', __('Hash'))
@section('content')

<div class="container page-normal">

<form method="POST" action="/hash">

	<div class="form-group">
		<input type="text" name="hash" id="hash" class="form-control" style="width: 90%; max-width:200px;" value="{{ $hash }}" autofocus />
	</div>

	<div id="flash" class="form-group">
		<span id='entry'>{{ $hashed }}</span>
		<a href='#' onclick="clipboardCopy(event, 'entry', 'entry')";>
			<span id="" class="glyphCustom glyphicon glyphicon-copy" style="font-size:1.3em; margin-left:5px; display:{{isset($hashed) && strlen($hashed) > 0 ? 'default' : 'none'}}"></span>
		</a>
		<span id='not-used-status'></span>
	</div>

	<div class="form-group">
		<button type="submit" name="submit" class="btn btn-primary">Submit</button>
	</div>

    @if (isset($hash))
        <div class="form-group">
            <span class="small-thin-text">{{hashQuick($hash)}}</span>
        </div>
	@endif

{{ csrf_field() }}
</form>

</div>

@endsection





