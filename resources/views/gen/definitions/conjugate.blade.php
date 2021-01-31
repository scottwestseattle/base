@extends('layouts.app')

@section('content')

<div class="container page-normal">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix, 'isAdmin' => $isAdmin])@endcomponent

	<div class="small-thin-text">Generated conjugations - <a href="/definitions/edit/{{$record->id}}">add to definition</a></div>

	<h3>{{$record->title}}</h3>

	<div class="">
		@if (isset($forms))
			<p style="">{{$forms}}</p>
		@endif
		
		@if (isset($records))
			
			<div class="mb-3">
			<h4>Participles:</h4>
			@foreach($records[CONJ_PARTICIPLE] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>
			
			<div class="mb-3">
			<h4>Indicative Present:</h4>
			@foreach($records[CONJ_IND_PRESENT] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>
			
			<div class="mb-3">
			<h4>Indicative Preterite:</h4>
			@foreach($records[CONJ_IND_PRETERITE] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>
			
			<div class="mb-3">
			<h4>Indicative Imperfect:</h4>
			@foreach($records[CONJ_IND_IMPERFECT] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>
			
			<div class="mb-3">
			<h4>Indicative Conditional:</h4>
			@foreach($records[CONJ_IND_CONDITIONAL] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>

			<div class="mb-3">
			<h4>Indicative Future:</h4>
			@foreach($records[CONJ_IND_FUTURE] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>
			
			<div class="mb-3">
			<h4>Subjunctive Present:</h4>
			@foreach($records[CONJ_SUB_PRESENT] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>
			
			<div class="mb-3">
			<h4>Subjunctive Imperfect:</h4>
			@foreach($records[CONJ_SUB_IMPERFECT] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>
			
			<div class="mb-3">
			<h4>Subjunctive Imperfect:</h4>
			@foreach($records[CONJ_SUB_IMPERFECT2] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>
			
			<div class="mb-3">
			<h4>Subjunctive Future:</h4>
			@foreach($records[CONJ_SUB_FUTURE] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>
			
			<div class="mb-3">
			<h4>Imperative:</h4>
			@foreach($records[CONJ_IMP_AFFIRMATIVE] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			<br/>
			@foreach($records[CONJ_IMP_NEGATIVE] as $conj)
				<span><i>{{$conj}}</i></span>
			@endforeach
			</div>

		@else
		
			@if (isset($status))
				<h4><i>{{$status}}</i></h4>
			@endif
		
		@endif
	<div>

</div>

@endsection

