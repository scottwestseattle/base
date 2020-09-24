@extends('layouts.app')

@section('content')

<div class="container page-normal">

	@component('translations.menu-submenu', ['prefix' => 'Translations'])@endcomponent

	<h1>@LANG('content.Translations')</h1>
				
	<?php $cnt = 0; $recs = $records['en']; ?>		
		
	<div class="table-responsive">
		
		<table style="min-width: 800px;">
			<tr><th></th><th>@LANG('ui.Key')</th><th>
			
			
	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active" href="#">@LANG('ui.English')</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#">@LANG('ui.Spanish')</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="#">@LANG('ui.Chinese')</a>
		</li>
	</ul>		
			
			
			
			</th></tr>
			@foreach($recs as $key => $value)
			<tr>
				<td>{{++$cnt}}.&nbsp;</td>
				<td>{{$key}}</td>
				<td>{{$records['en'][$key]}}</td>
				<!-- td>{{$records['es'][$key]}}</td>
				<td>{{$records['zh'][$key]}}</td -->
			<tr>
			@endforeach
		</table>
		
	</div>
</div>

@stop
