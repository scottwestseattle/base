@php
	$recs = $records['en'];
@endphp
@extends('layouts.app')
@section('title', 'View Translations - ' . $filename)
@section('menu-submenu')@component('translations.menu-submenu', ['record' => $filename]) @endcomponent @endsection
@section('content')

<h1>@LANG('ui.Translations')</h1>

<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item" role="presentation">
		<a class="nav-link active" id="english-tab" data-toggle="tab" href="#english" role="tab" aria-controls="english" aria-selected="true">@LANG('ui.English')</a>
	</li>
	<li class="nav-item" role="presentation">
		<a class="nav-link" id="spanish-tab" data-toggle="tab" href="#spanish" role="tab" aria-controls="spanish" aria-selected="false">@LANG('ui.Spanish')</a>
	</li>
	<li class="nav-item" role="presentation">
		<a class="nav-link" id="chinese-tab" data-toggle="tab" href="#chinese" role="tab" aria-controls="chinese" aria-selected="false">@LANG('ui.Chinese')</a>
	</li>
</ul>

<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="english" role="tabpanel" aria-labelledby="english-tab">
		<div class="table-responsive width100">
		<table>
			<tr><th></th><th>@LANG('ui.Key')</th><th>@LANG('ui.Translation')</th></tr>
			@foreach($recs as $key => $value)
			<tr>
				<td>{{$loop->iteration}}.&nbsp;</td>
				<td>{{$key}}</td>
				<td>{{$records['en'][$key]}}</td>
			<tr>
			@endforeach
		</table>
		</div>
	</div>
	
	<div class="tab-pane fade" id="spanish" role="tabpanel" aria-labelledby="spanish-tab">
		<div class="table-responsive width100">
		<table>
			<tr><th></th><th>@LANG('ui.Key')</th><th>@LANG('ui.Translation')</th></tr>
			@foreach($recs as $key => $value)
			<tr>
				<td>{{$loop->iteration}}.&nbsp;</td>
				<td>{{$key}}</td>
				<td>{{$records['es'][$key]}}</td>
			<tr>
			@endforeach
		</table>
		</div>
	</div>
	
	<div class="tab-pane fade" id="chinese" role="tabpanel" aria-labelledby="chinese-tab">
		<div class="table-responsive width100">
		<table>
			<tr><th></th><th>@LANG('ui.Key')</th><th>@LANG('ui.Translation')</th></tr>
			@foreach($recs as $key => $value)
			<tr>
				<td>{{$loop->iteration}}.&nbsp;</td>
				<td>{{$key}}</td>
				<td>{{$records['zh'][$key]}}</td>
			<tr>
			@endforeach
		</table>
		</div>
	</div>
</div>	

@endsection
