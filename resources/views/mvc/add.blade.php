@extends('layouts.app')
@section('title', __('base.Add MVC'))
@section('menu-submenu')@component('mvc.menu-submenu')@endcomponent @endsection
@section('content')
<div class="container">
                <h1>Add MVC</h1>
               
<form method="POST" action="/mvc/create">

	<div class="form-group col-md-6">
		<label for="model">Enter Model Name (singular, capitalized)</label>
		<input id="model" class="form-control @error('model') is-invalid @enderror" name="model" placeholder="" required autofocus>

		@error('model')
			<span class="invalid-feedback" role="alert">
				<strong>{{ $message }}</strong>
			</span>
		@enderror
	</div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Add</button>
    </div>
{{ csrf_field() }}
</form>


</div>
@endsection
