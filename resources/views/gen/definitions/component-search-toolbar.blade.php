@php
    $locale = app()->getLocale();
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

    @if (isAdmin() || App\User::isOwner($record->user_id))
        @if (isAdmin())
    		<div class="middle ml-2"><a href='' onclick="toggleWip(event, {{$record->id}}, '#{{$status}}');"><span id="{{$wipId}}" class="glyphCustom-md glyphicon glyphicon-{{$finished}}"></span></a></div>
		@endif
		<div class="middle ml-2"><a href="{{route('definitions.edit', ['locale' => $locale, 'definition' => $record->id])}}"><span class="glyphCustom-md glyphicon glyphicon-edit"></span></a></div>
		<div class="middle ml-2">@component('components.control-delete-glyph', ['svg' => 'trash-fill', 'href' => route('definitions.delete', ['locale' => $locale, 'definition' => $record->id]), 'prompt' => 'ui.Confirm Delete'])@endcomponent</div>
	@endif

    @if ($record->rank > 0)
        <div class="middle">@component('components.badge', ['text' => '#' . $record->rank])@endcomponent</div>
    @endif
</div>
<div style="clear:both;" id="{{$status}}" class="small-thin-text red"></div>
