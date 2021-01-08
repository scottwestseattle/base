@extends('layouts.app')

@section('content')

<div class="container page-normal">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix, 'isAdmin' => $isAdmin])@endcomponent

	<div class="page-nav-buttons">
		<a class="btn btn-success btn-md" role="button" href="/{{$prefix}}/">@LANG('content.Back to List')
		<span class="glyphicon glyphicon-button-back-to"></span>
		</a>
	</div>

	<h1>View</h1>
	
	<h3 name="title" class="">{{$record->title }}</h3>

	<p>{{$record->description }}</p>

</div>
@endsection
