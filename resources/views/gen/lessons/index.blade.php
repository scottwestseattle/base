@php
    $prefix = 'lessons';
@endphp
@extends('layouts.app')
@section('title', trans_choice('proj.Lesson', 2) )
@section('menu-submenu')@component('gen.lessons.menu-submenu', ['prefix' => $prefix])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>@LANG('content.' . $titlePlural) ({{count($records)}})

	@if (isAdmin())
		<span style="font-size:.6em;"><a href="/{{$prefix}}/admin"><span class="glyphCustom glyphicon glyphicon-admin"></span></a></span>
	@endif
	</h1>

	<div class="row" style="margin-bottom:10px;">
		@foreach($records as $record)
		@php
            $status = App\Status::getWipStatus($record->wip_flag);
        @endphp
		<div style="padding:10px;" class="col-sm-4"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
			<div class="drop-box" style="height:200px;  background-color: #4993FD; color:white;" ><!-- inner col div -->
                <a style="height:100%; width:100%;" class="btn {{$status['class']}} btn-lg" role="button" href="/{{$prefix}}/view/{{$record->id}}">
                    {{$record->getDisplayNumber()}}&nbsp;{{$record->title}}<br/>{{ $record->description}}
                </a>
			</div><!-- inner col div -->
		</div><!-- outer col div -->
		@endforeach
	</div><!-- row -->

</div>

@endsection
