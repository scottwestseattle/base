@php
$label_class = isset($label_class) ? $label_class : null;
$select_class = isset($select_class) ? $select_class : null;
$onchange = isset($onchange) ? $onchange : null;
$selected_option = isset($selected_option) ? $selected_option : null;
@endphp
@if (isset($options) && count($options) > 0)

	@if (isset($label))

		@if (isset($label_div))
			<div>
		@endif

		<label for="{{$field_name}}" class="{{$label_class}}">{{$label}}</label>

		@if (isset($label_div))
			</div>
		@endif

	@endif

	<select class="{{$select_class}}" id="{{$field_name}}" name="{{$field_name}}" onchange="{{$onchange}}">

	@if (isset($empty))
		<option value="0">({{$empty}})</option>
	@endif

	@foreach ($options as $key => $value)
		@if (isset($selected_option) && $key == $selected_option)
			<option value="{{$key}}" selected>@LANG('geo.' . $value)</option>
		@else
			<option value="{{$key}}">@LANG('geo.' . $value)</option>
		@endif
	@endforeach

	</select>

@endif
