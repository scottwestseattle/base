@extends('layouts.app')
@section('title', trans_choice('ui.Translation', 2))
@section('menu-submenu')@component('translations.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('ui.Translation', 2)}} ({{ count($records) }})</h1>

	<div class="table-responsive">
	<table class="table table-striped table-translations">
		<tbody>
		@foreach($records as $record)
			<tr>
				<td class="icon"><a href='/translations/edit/{{$record}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td><a href="/translations/view/{{$record}}">{{$record}}</a></td>
				<td class="icon"><a href='/translations/delete/{{$record}}'>@component('components.icon-delete')@endcomponent</a></td>
			</tr>
		@endforeach
		</tbody>
	</table>

	</div>

</div>
@endsection
