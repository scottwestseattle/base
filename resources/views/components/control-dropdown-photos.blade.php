@if (isset($options) && count($options) > 0)

	@if (isset($prompt))

		@if (isset($prompt_div))
			<div>
		@endif

		<label for="{{$field_name}}">{{$prompt}} ({{count($options)}}):</label>

		@if (isset($prompt_div))
			</div>
		@endif

	@endif

@if (false)
	@if (isset($onchange))
		<select name="{{$field_name}}" id="{{$field_name}}" class="{{$select_class}}" onchange="{{$onchange}}">
	@else
		<select name="{{$field_name}}" id="{{$field_name}}" class="{{$select_class}}" >
	@endif

	@if (isset($empty))
		<option value="{{$noSelection}}">({{$empty}})</option>
	@endif

	@foreach ($options as $key => $value)
		@if (isset($selected_option) && $key == $selected_option)
			<option value="{{$key}}" selected>{{$value}}</option>
		@else
			<option value="{{$key}}">{{$value}}</option>
		@endif
	@endforeach

	</select>
@endif

	@foreach ($options as $key => $value)
	    <img id="{{$value}}" src="/img/plancha/{{$value}}" width="75"
	        onclick="event.preventDefault(); {{$onchange}}('{{$value}}');"
	        title="{{$value}}"
	        alt="{{$value}}" />
    @endforeach

@endif
