@if (isset($record) && isset($record->conjugations) && is_array($record->conjugations))
	<?php $i = 0; ?>
	<div class="small-thin-hdr mt-2 mb-1">Participles</div>
	<div class="small-thin-text mb-2">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-hdr mt-2 mb-1">Indicative</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-hdr mt-2 mb-1">Subjunctive</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-hdr mt-2 mb-1">Imperative</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
	<div class="small-thin-text mb-1">{{$record->conjugations[$i++]}}</div>
@endif