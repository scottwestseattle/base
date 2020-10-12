@extends('layouts.app')
@section('title', __('base.MVC'))
@section('menu-submenu')@component('mvc.menu-submenu')@endcomponent @endsection
@section('content')
<div class="">
	<h1>@LANG('base.MVC')</h1>
	
	@guest
	@else
		<p><a href="/mvc/add">Add MVC</a></p>
	@endguest
	
	<h3>Generated MVC</h3>
	<div  class="table-responsive">
	<table class="table">
		<tbody>
		@foreach($files as $file)
			@if (strlen($file) > 2 && $file != 'templates')
			<tr>
				<td><a href="/{{$file}}">{{$file}}</a></td>
				<td class="glyphicon-width"><a href='/mvc/confirmdelete/{{$file}}'><span class="glyphCustom-sm glyphicon glyphicon-trash"></span></a></td>
			</tr>
			@endif
		@endforeach
		</tbody>
	</table>
	</div>
</div>
@endsection
