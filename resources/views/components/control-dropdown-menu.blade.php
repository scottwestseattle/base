@if (isset($options) && count($options) > 0)

	@if (isset($prompt))

		@if (isset($prompt_div))
			<div>
		@endif

		<label for="{{$field_name}}">{{$prompt}}</label>

		@if (isset($prompt_div))
			</div>
		@endif

	@endif

	@if (isset($onchange))
		<select name="{{$field_name}}" id="{{$field_name}}" class="{{$select_class}}" onchange="{{$onchange}}">
	@else
		<select name="{{$field_name}}" id="{{$field_name}}" class="{{$select_class}}" >
	@endif

	@if (isset($empty))
		<option value="0">({{$empty}})</option>
	@endif

	@foreach ($options as $key => $value)
		@if (isset($selected_option) && $key == $selected_option)
			<option value="{{$key}}" selected>{{__($value)}}</option>
		@else
			<option value="{{$key}}">{{__($value)}}</option>
		@endif
	@endforeach

	</select>

@endif
