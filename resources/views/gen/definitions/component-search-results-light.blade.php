	<div class="table" style="">
		<table id="searchDefinitionsResultsTable" class="table-responsive xtable-striped table-condensed large-text" style="">
			<tbody>
				@foreach($records as $record)
				<tr>
					<td>
						<a class="float-left" href="/definitions/view/{{$record->permalink}}">{{$record->title}}</a>
					</td>
					<td style="width:100%;">
                        {{$record->translation_en}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
<!-- end of repeat block -->
