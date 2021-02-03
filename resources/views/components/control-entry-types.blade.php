<div class="form-group">
	<label for="type_flag">Entry Type:&nbsp;</label>		
	<select name="type_flag" id="type_flag">
		@foreach ($entryTypes as $key => $value)
			@if (isset($current_type) && intval($key) === intval($current_type))
				<option value="{{$key}}" selected>{{$value}}</option>
			@else
				<option value="{{$key}}">{{$value}}</option>
			@endif
		@endforeach
	</select>
</div>					
