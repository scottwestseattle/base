@extends('layouts.app')

@section('content')

<div class="container page-normal">

	<h1>@LANG('content.' . $titlePlural) ({{count($records)}})
	@if ($isAdmin)
		<span style="font-size:.6em;"><a href="/{{$prefix}}/admin"><span class="glyphCustom glyphicon glyphicon-admin"></span></a></span>
	@endif	
	</h1>
	
	<div class="row" style="margin-bottom:10px;">		
		@foreach($records as $record)			
		<div style="xmax-width: 400px; padding:10px;" class="col-sm-4"><!-- outer div needed for the columns and the padding, otherwise they won't center -->

			<div class="drop-box" style="height:200px;  background-color: #4993FD; color:white;" ><!-- inner col div -->
	
				@if ( ($status=$record->getStatus())['done'] || !$isAdmin )
					<a style="background-color: #4993FD; height:100%; width:100%;" class="btn btn-primary btn-lg" role="button" href="/{{$prefix}}/view/{{$record->id}}">
						{{$record->title}}<br/>{{ $record->description}}
					</a>
				@else
					<a style="height:100%; width:100%;" class="btn {{$status['btn']}} btn-lg" role="button" href="/{{$prefix}}/view/{{$record->id}}">
						{{$record->title}}<br/>{{ $record->description}}
					</a>
				@endif
					
			</div><!-- inner col div -->			
			
		</div><!-- outer col div -->
		@endforeach		
	</div><!-- row -->														

</div>

@endsection
