@extends('layouts.app')

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
	
	<table class="table table-striped">
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="glyphicon-width"><a href='/translations/edit/{{$record}}'><span class="glyphCustom-sm glyphicon glyphicon-edit"></span></a></td>
				<td><a href="/translations/view/{{$record}}">{{$record}}</a></td>
@if (false)				
				<td class="glyphicon-width"><a href='/translations/confirmdelete/{{$record}}'><span class="glyphCustom-sm glyphicon glyphicon-delete"></span></a></td>
@endif
			</tr>
		@endforeach
		</tbody>
	</table>
	
	</div>
	
</div>
@endsection
