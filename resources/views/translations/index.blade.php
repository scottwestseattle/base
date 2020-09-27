@extends('layouts.app')
@section('title', 'Translations')
@section('menu-submenu')@component('translations.menu-submenu')@endcomponent @endsection
@section('content')

<div class="container page-normal">

@if (false)
	<!-- Sub-menu ------>
	<div class="" style="font-size:20px;">
		<table class=""><tr>			
			<td style="width:40px;"><a href='/translations/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>			
		</tr></table>
	</div>			
@endif
	
	<h1>@LANG('ui.Translations') ({{ count($records) }})</h1>

	<div class="table-responsive">
	<table class="table table-striped table-translations">
		<tbody>
		@foreach($records as $record)
			<tr>
				<td><a href='/translations/edit/{{$record}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td><a href="/translations/view/{{$record}}">{{$record}}</a></td>
				<td><a href='/translations/delete/{{$record}}'>@component('components.icon-delete')@endcomponent</a></td>
			</tr>
		@endforeach
		</tbody>
	</table>
	
	</div>
	
</div>
@endsection
