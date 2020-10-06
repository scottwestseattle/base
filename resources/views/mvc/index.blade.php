@extends('layouts.app')
@section('title', __('base.MVC'))
@section('menu-submenu')@component('mvc.menu-submenu')@endcomponent @endsection
@section('content')
<div class="">
	<h1>@LANG('base.MVC')</h1>
	<p><a href="/mvc/add">Add MVC</a></p>
	
	<h3>Views</h3>
	<ul>
	@foreach($files as $file)
		@if (strlen($file) > 2)
			<li><a href="/{{$file}}">{{$file}}</a></li>
		@endif
	@endforeach
	</ul>
</div>
@endsection
