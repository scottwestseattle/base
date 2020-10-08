@extends('layouts.app')
@section('title', __('base.View MVC'))
@section('menu-submenu')@component('mvc.menu-submenu')@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('base.View MVC')}}</h1>
	<h3 name="title" class="">{{ucfirst($model)}}</h3>
	<p>Routes for {{$model}} model have been appended to "/routes/web.php"</p>
	<p>Add table to database and then these links will work:
	<ul>
		<li><a href="/{{$views}}">{{ucfirst($model)}}</a></li>
		<li><a href="/{{$views}}/add">Add {{ucfirst($model)}}</a></li>
	</ul>
	<h3>MySQL Table Schema</h3>
	<p>{!!nl2br($schemaMysql)!!}</p>
</div>
@endsection

