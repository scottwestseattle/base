@extends('layouts.app')
@section('title', __('base.Add MVC'))
@section('menu-submenu')@component('mvc.menu-submenu')@endcomponent @endsection
@section('content')
<div class="container">
                <h1>Add MVC</h1>
               
<form method="POST" action="/mvc/create">

    <div class="form-group">
        <input type="text" name="model" class="form-control" placeholder="Model"></input>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Add</button>
    </div>
{{ csrf_field() }}
</form>


</div>
@endsection
