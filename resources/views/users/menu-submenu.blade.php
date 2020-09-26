@if (is_admin())
<div class="submenu-view">
	<table>
		<tr>
			<td><a href='/users/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			@if (isset($record))
			<td><a href='/users/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
			<td><a href='/users/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
			<td><a href='/users/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			@endif
		</tr>
	</table>
</div>		
@endif