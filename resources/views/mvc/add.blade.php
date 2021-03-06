@extends('layouts.app')
@section('title', __('base.Add MVC'))
@section('menu-submenu')@component('mvc.menu-submenu')@endcomponent @endsection
@section('content')
<div class="container">
                <h1>Add MVC</h1>

<form method="POST" action="/mvc/create">

	<p>Model Template: {{$paths['modelTpl']}}</p>
	<p>Controller Template: {{$paths['controllerTpl']}}</p>
	<p>Views Templates: {{$paths['viewsTplPath']}}</p>

	<div class="form-group col-md-6">
		<label for="model">Enter Model Name (singular, capitalized)</label>
		<input id="model" class="form-control @error('model') is-invalid @enderror" name="model" onblur="$('#plural').val($(this).val().toLowerCase() + 's');" required autofocus>

		@error('model')
			<span class="invalid-feedback" role="alert">
				<strong>{{ $message }}</strong>
			</span>
		@enderror
	</div>

	<div class="form-group col-md-6">
		<label for="plural">View Folder and Database Table Name (plural, lower-case)</label>
		<input id="plural" class="form-control @error('plural') is-invalid @enderror" name="plural" required>

		@error('model')
			<span class="invalid-feedback" role="alert">
				<strong>{{ $message }}</strong>
			</span>
		@enderror
	</div>

	<div class="form-group">
		<input type="checkbox" name="add_routes" id="add_routes" class="ml-3" />
		<label for="add_routes" class="checkbox-label control-label">@LANG('base.Add Routes to') web.php</label>
		<input type="checkbox" name="topLevel" id="topLevel" class="ml-3" />
		<label for="topLevel" class="checkbox-label control-label">@LANG('base.Top Level (not Gen)')</label>
	</div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Add</button>
    </div>
{{ csrf_field() }}
</form>

</div>
@endsection

