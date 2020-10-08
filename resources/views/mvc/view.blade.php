@extends('layouts.app')
@section('title', __('base.View') . ' MVC')
@section('menu-submenu')@component('mvc.menu-submenu')@endcomponent @endsection
@section('content')
<div class="">
	<h1>{{__('base.View')}} MVC</h1>
	<h3 name="title" class="">{{ucfirst($model)}} MVC {{__('base.Generated')}}</h3>
	<ul>
		<li>{{__('base.Model')}}: {{$paths['modelOut']}}</li>
		<li>{{__('base.Controller')}}: {{$paths['controllerOut']}}</li>
		<li>MySQL {{__('base.Table Schema')}}: "{{$paths['mysqlSchemaOut']}}"</li>
		<li>{{__('base.Routes appended to')}} "/routes/web.php"</li>
	</ul>
	<p>{{__('base.Add table to database and then these links will work')}}:
	<ul>
		<li><a href="/{{$views}}">{{ucfirst($model)}}</a></li>
		<li><a href="/{{$views}}/add">Add {{ucfirst($model)}}</a></li>
	</ul>
	<h3>MySQL {{__('base.Table Schema')}}</h3>
	<div class="text-sm"><a href="" onclick="event.preventDefault(); select('schema');">select schema</a></div>
	<p id="schema">{!!nl2br($schemaMysql)!!}</p>
</div>
<script>

function select(id)
{
	var range = document.createRange();
	var selection = window.getSelection();
	range.selectNodeContents(document.getElementById(id));

	selection.removeAllRanges();
	selection.addRange(range);	
}

</script>
@endsection

