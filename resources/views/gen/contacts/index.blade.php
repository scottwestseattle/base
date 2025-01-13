@extends('layouts.app')
@section('title', trans_choice('ui.Contact', 2))
@section('menu-submenu')@component('gen.contacts.menu-submenu')@endcomponent @endsection
@section('content')
<div>
	<h1>{{trans_choice('ui.Contact', 2)}} ({{count($records)}})</h1>
	<div  class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th>{{__('ui.Name')}}</th>
				<th>{{__('ui.Account')}}</th>
				<th>{{__('ui.Updated')}}</th>
@if (false)
				<th>{{__('ui.Notes')}}</th>
@endif
				<th></th>
			</tr>
		</thead>
		<tbody>
		@foreach($records as $record)
			<tr>
			    <td>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                    <td class="icon"><a href='' onclick="event.preventDefault(); $('.row-info').hide(); $('.row{{$loop->index}}').show()"><span class="glyphicon glyphicon-collapse-down"></span></a></td>
                    <td><a href="/contacts/show/{{$record->id}}">{{$record->name}}</a></td>
                    <td>{{$record->access}}</td>
                    <td>{{$record->lastUpdated}}</td>
    @if (false)
                    <td>{{Str::limit($record->notes, DESCRIPTION_LIMIT_LENGTH)}}</td>
    @endif
                    <td class="icon"><a href='/contacts/edit/{{$record->id}}'>@component('components.icon-edit')@endcomponent</a></td>
                    <td class="icon">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => '/contacts/delete/' . $record->id . '', 'prompt' => 'ui.Confirm Delete'])@endcomponent</td>
                    <!-- td class="icon"><a href='/contacts/confirmdelete/{{$record->id}}'>@component('components.icon', ['svg' => 'trash-fill'])@endcomponent</a></td -->
                            </tr>
                            <tr class="row-info row{{$loop->index}} hidden">
                                <td colspan="6"><b>Verify:</b> {{$record->verifyMethod}}<span class="ml-5"><b>Address:</b> {{$record->address}}</span></td>
                            </tr>
                            @if (isset($record->notes))
                            <tr class="row-info row{{$loop->index}} hidden">
                                <td colspan="6">{{$record->notes}}</td>
                            </tr>
                            @endif
                            @if (isset($record->numbers))
                            <tr class="row-info row{{$loop->index}} hidden">
                                <td colspan="6">{{$record->numbers}}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
			    </td>
			</tr>
		@endforeach
		</tbody>
	</table>
	</div>

</div>

@endsection
