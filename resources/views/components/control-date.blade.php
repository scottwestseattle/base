@if (isset($div) && $div)
<div class="form-group">
@endif

	@if (isset($prompt))
	<label for="{{$field_name}}">{{$prompt}}</label>
	@endif
		
	<a href='#' onclick="event.preventDefault(); javascript:changeDate(-1, 'year', 'month', 'day'); @if(isset($formId))$('#{{$formId}}').submit();@endif";>
		<span id="" class="glyphCustom glyphicon glyphicon-minus-sign" style="font-size:1.3em; margin-left:5px;"></span>
	</a>						

	<select name="month" id="month">
			<option value="0">(month)</option>
		@foreach ($months as $key => $value)
			@if (isset($filter['selected_month']) && $key == $filter['selected_month'])
				<option value="{{$key}}" selected>{{$value}}</option>
			@else
				<option value="{{$key}}">{{$value}}</option>
			@endif
		@endforeach
	</select>
	
	@if (isset($days))
	<select name="day" id="day">
			<option value="0">(day)</option>
		@foreach ($days as $key => $value)
			@if (isset($filter['selected_day']) && $key == $filter['selected_day'])
				<option value="{{$key}}" selected>{{$value}}</option>
			@else
				<option value="{{$key}}">{{$value}}</option>
			@endif
		@endforeach
	</select>
	@endif

	<select name="year" id="year">
			<option value="0">(year)</option>
		@foreach ($years as $key => $value)
			@if (isset($filter['selected_year']) && $key == $filter['selected_year'])
				<option value="{{$key}}" selected>{{$value}}</option>
			@else
				<option value="{{$key}}">{{$value}}</option>
			@endif
		@endforeach
	</select>

	<a href='#' onclick="event.preventDefault(); javascript:changeDate(1, 'year', 'month', 'day'); @if(isset($formId))$('#{{$formId}}').submit();@endif";>
		<span id="" class="glyphCustom glyphicon glyphicon-plus-sign" style="font-size:1.3em; margin-left:5px;"></span>
	</a>						
	<a href='#' onclick="event.preventDefault(); javascript:changeDate(0, 'year', 'month', 'day')";>
		<span id="" class="glyphCustom glyphicon glyphicon-remove" style="font-size:1.3em; margin-left:5px;"></span>
	</a>						
	<a href='#' onclick="event.preventDefault(); javascript:changeDate(99, 'year', 'month', 'day', true); @if(isset($formId))$('#{{$formId}}').submit();@endif";>
		<span id="" class="glyphCustom glyphicon glyphicon-calendar" style="font-size:1.3em; margin-left:5px;"></span>
	</a>

	@if (isset($monthCheckbox) && $monthCheckbox)
	<input type="checkbox" name="month_flag" id="month_flag" class="form-control-inline" value="1" {{ $filter['month_flag'] == 1 ? 'checked' : '' }} />
	<label for="month_flag" class="checkbox-label">Month</label>
	@endif
	
@if (isset($div) && $div)	
</div>	
@endif


