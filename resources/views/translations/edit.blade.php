@php
	$cnt = 0;
	$recs = $records['en'];
@endphp
@extends('layouts.app')
@section('title', 'Edit Translations - ' . $filename)
@section('menu-submenu')@component('translations.menu-submenu', ['record' => $filename])@endcomponent @endsection
@section('content')

<div>

	<h1>@LANG('ui.Edit') @LANG('ui.Translations')</h1>

	<form method="POST" action="/translations/update/{{$filename}}">
					
		<div class="form-group">		
			
		@if (is_array($recs))
		<table>
			<tr><th></th><th>@LANG('ui.Key')</th><th>@LANG('ui.English')</th><th>@LANG('ui.Spanish')</th><th>@LANG('ui.Chinese')</th></tr>
			@foreach($recs as $key => $value)
				<tr>
				<td>{{$cnt + 1}}.</td>
				<td style=""><input type="text" name="records[0][{{$cnt}}]" class="form-control" value="{{$key}}"></input></td>
				<td style=""><input type="text" name="records[1][{{$cnt}}]" class="form-control" value="{{$records['en'][$key]}}"></input></td>
				<td style=""><input type="text" name="records[2][{{$cnt}}]" class="form-control" value="{{$records['es'][$key]}}"></input></td>
				<td style=""><input type="text" name="records[3][{{$cnt++}}]" class="form-control" value="{{$records['zh'][$key]}}"></input></td>
				<tr>
			@endforeach
		</table>
		@endif
		
		<h3>@LANG('ui.Add')</h3>
		
		<table>
			<tr><th>@LANG('ui.Key')</th><th>@LANG('ui.English')</th><th>@LANG('ui.Spanish')</th><th>@LANG('ui.Chinese')</th></tr>
		@for($i = $cnt; $i < ($cnt + 5); $i++)
			<tr>
			<td style=""><input type="text" name="records[0][{{$i}}]" class="form-control"></input></td>
			<td style=""><input type="text" name="records[1][{{$i}}]" class="form-control"></input></td>
			<td style=""><input type="text" name="records[2][{{$i}}]" class="form-control"></input></td>
			<td style=""><input type="text" name="records[3][{{$i}}]" class="form-control"></input></td>
			<tr>
		@endfor
		</table>

		</div>
			
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop
