<div>
	@if (isset($records) && count($records) > 0)
	<div style="text-align:left;">
		<!-- repeat this block for each column -->
			<div class="table" style="font-size: 13px; background-color:white;">
				@if (count($records) > 3)
					<div class="text-center small-thin-hdr mb-2">
						Definitions
						@component('components.badge', ['text' => count($records), 'class' => 'badge-blue badge-small'])@endcomponent
					</div>
				@endif
				<table class="table-striped" style="width:100%;">
					<tbody>
						@foreach($records as $record)
						<tr>
							<td class="hidden-xs" style=""><!-- SM and higher -->
								<a class="" href="/definitions/show/{{$record->id}}" target="_blank">{{$record->title}}</a>
							</td>
							<td style="padding-top: 2px;"><!-- XS -->
								<div class="hidden-lg hidden-md hidden-sm">
									<a href="/definitions/show/{{$record->id}}" target="_blank">{{$record->title}}</a>
								</div>

								@if (isset($record->translation_en))
									<div class="mt-2 steelblue">{!!nl2br($record->translation_en)!!}</div>
								@elseif (App\User::isSuperAdmin())
									<a href="/definitions/edit/{{$record->id}}" target="_blank" class="small-thin-text danger">add translation</a>
								@endif

							</td>

							<td style="width:20px;">
								<a onclick="event.preventDefault(); removeDefinitionUser('/entries/remove-definition-user-ajax/{{$entryId}}/{{$record->id}}');" href=''>
								<span class="glyphCustom-sm glyphicon glyphicon-remove small-thin-text mediumgray"></span></a>
							</td>

						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		<!-- end of repeat block -->
	</div>
	@else
		<div class="text-center">{{__('proj.Vocabulary list is empty')}}</div>
	@endif
</div>
