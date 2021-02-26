@php
	$finished = $record->isFinished() ? 'ok-circle' : 'remove-sign';
	$status = 'status' . $id . '-' . $record->id . '';
	$wipId = 'wip' . $id . '-' . $record->id . '';
@endphp

<div class="float-left">

    @component('gen.definitions.component-heart', [
        'record' => $record,
        'id' => $id,
        'lists' => $lists,
        'status' => $status,
    ])@endcomponent

	@if (isAdmin())
		<div class="middle ml-2">
			<a href='' onclick="toggleWip(event, {{$record->id}}, '#{{$status}}');">
				<span id="{{$wipId}}" class="glyphCustom-md glyphicon glyphicon-{{$finished}}"></span>
			</a>
		</div>
		<div class="middle ml-2"><a href='/definitions/edit/{{$record->id}}'><span class="glyphCustom-md glyphicon glyphicon-edit"></span></a></div>
		<div class="middle ml-2"><a href='/definitions/confirmdelete/{{$record->id}}'><span class="glyphCustom-md glyphicon glyphicon-delete"></span></a></div>
	@endif
    @if ($record->rank > 0)
        <div class="middle">@component('components.badge', ['text' => '#' . $record->rank])@endcomponent</div>
    @endif
</div>
<div style="clear:both;" id="{{$status}}" class="small-thin-text red"></div>
