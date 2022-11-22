@extends('layouts.app')
@section('title', trans_choice('proj.Stat', 2))
@section('menu-submenu')@component('gen.stats.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('proj.Stat', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th><th>{{__('ui.ID')}}</th><th>{{trans_choice('ui.User', 1)}}</th><th>{{trans_choice('proj.Definition', 1)}}</th><th>{{trans_choice('ui.View', 2)}}</th><th>Reads</th><th>Score</th><th>Last QnA</th><th>Last View</th><th>{{__('ui.Updated')}}</th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
		    @php
		        $score = floatval(number_format($record->qna_score * 100.0, 1));
		    @endphp
			<tr>
				<td class="icon"><a href='/stats/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
				<td>{{$record->id}}</td>
				<td>{{$record->user_id}}</td>
				<td>{{$record->definition_id}}</td>
				<td>{{$record->views}}</td>
				<td>{{$record->reads}}</td>
				<td>{{$score}}% ({{$record->qna_correct}}/{{$record->qna_attempts}})</td>
				<td class="date-sm">{{$record->qna_at}}</td>
				<td class="date-sm">{{$record->viewed_at}}</td>
				<td class="date-sm">{{$record->updated_at}}</td>
				<td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/stats/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
				<td class="icon"><a href='/stats/confirmdelete/{{$record->id}}'>@component('components.icon', ['svg' => 'trash-fill'])@endcomponent</a></td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
