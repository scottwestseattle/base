@extends('layouts.app')
@section('title', __('base.MVC'))
@section('menu-submenu')@component('mvc.menu-submenu')@endcomponent @endsection
@section('content')
<div class="">
	<h1>@LANG('base.MVC')</h1>

	@guest
	@else
		<p><a href="/mvc/add">Add MVC</a></p>
		<p><a href="/templates">Templates</a></p>
	@endguest

	<h3>Generated MVC</h3>
	<div  class="table-responsive">
	<table class="table">
		<tbody>
		@foreach($files as $file)
			<!-- skip the . and the .. -->
			@if ($file != 'templates')
			<tr>
				<td style="width:50px;"><a href="/{{$file}}">{{ucfirst($file)}}</a></td>
				<td><a href="/mvc/view/{{Str::singular($file)}}/{{$file}}">Files</a></td>
				<td class="icon"><a href='/mvc/confirmdelete/{{$file}}'>@component('components.icon-delete')@endcomponent</a></td>
			</tr>
			@endif
		@endforeach
		</tbody>
	</table>
	</div>
</div>
@endsection
