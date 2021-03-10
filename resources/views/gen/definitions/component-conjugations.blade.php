@if (isset($record) && isset($record->conjugations) && is_array($record->conjugations))
	@php
	    $i = 0;
	@endphp
	<div class="small-thin-hdr mt-2 mb-1"><a href="/verbs/conjugation/{{$record->title}}">{{trans_choice('proj.Conjugation', 2)}}</a></div>

	<div class="small-thin-h2 mt-2 mb-1">{{trans_choice('proj.Participle', 2)}}</div>
	<div class="small-thin-text mb-2">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Indicative')}} - {{__('proj.Present')}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Indicative')}} - {{__('proj.Preterite')}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Indicative')}} - {{__('proj.Imperfect')}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Indicative')}} - {{__('proj.Conditional')}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Indicative')}} - {{__('proj.Future')}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Subjunctive')}} - {{__('proj.Present')}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Subjunctive')}} - {{__('proj.Imperfect')}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Subjunctive')}} - {{__('proj.Imperfect')}} 2</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Subjunctive')}} - {{__('proj.Future')}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Imperative')}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>

	<div class="small-thin-h2 mt-2 mb-1">{{__('proj.Imperative Negative')}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
@endif
