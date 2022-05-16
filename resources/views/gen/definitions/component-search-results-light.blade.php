    @if (true)
    @php
        $count = count($records);
        $matches = ($count > 1 || $count == 0) ? 'matches' : 'match';
    @endphp
	<div class="table" style="" id="searchDefinitionsResultsTable">
		<table  class="table-responsive table-condensed medium-text" style="">
		    <thead>
		        <tr>
		            <td style="xmin-width: 100px;">
		                {{$count}} {{$matches}}
		            </td>
		            <td>
		                <input id="searchAll" name="searchAll" type="checkbox" style="position:static;" />
		                <label for="searchAll">Search All</label>
		            </td>
		        </tr>
		    </thead>
        </table>
		<table  class="table-responsive table-condensed medium-text" style="">
		    <thead>
		    </thead>
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
	@else
 		<div class="mt-1 middle xdropdown">
			<ul style="float: left; background-color:transparent; border:0;"  class="xdropdown-menu">
				@foreach($records as $record)
    				<li>{{$record->title . ' - ' . $record->translation_en}}</li>
				@endforeach
			</ul>
		</div>
	@endif
