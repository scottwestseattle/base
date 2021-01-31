@extends('layouts.app')

@section('content')

<div class="container page-normal">

	@if (isset($record->conjugations))
	<h3>{{$record->title}}</h3>

        <div class="mb-3">
        <h4>Participles:</h4>
        @foreach($record->conjugations['tenses'] as $r)
            <h3>{{$headers[$loop->index + 1]}}</h3>
            @foreach($r as $p)
                <div><i>{{$p}}</i></div>
            @endforeach
        @endforeach
        </div>

	@else

        <div class="mt-2">
            <h3>@LANG('content.Verb not found')</h3>
        </div>

	@endif

</div>

@endsection

