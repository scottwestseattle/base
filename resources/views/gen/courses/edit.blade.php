@extends('layouts.app')

@section('content')

<div class="container page-normal">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix, 'isAdmin' => $isAdmin])@endcomponent

	<h1>@LANG('ui.Edit') @LANG('content.' . $title)</h1>

	<form method="POST" id="form-edit" action="/{{$prefix}}/update/{{$record->id}}">

		<div class="form-group">
		@component('components.control-dropdown-menu', ['record' => $record, 'prefix' => $prefix, 
			'isAdmin' => $isAdmin, 
			'prompt' => 'Course Type: ',
			'empty' => 'Select Course Type',
			'options' => App\Course::getTypes(),
			'selected_option' => $record->type_flag,
			'field_name' => 'type_flag',
			'prompt_div' => true,
			'select_class' => 'form-control form-control-sm',
		])@endcomponent
		</div>

		@component('components.control-accent-chars-esp', ['visible' => true, 'flat' => true])@endcomponent																					   

		<div class="form-group">
			<label for="title" class="control-label">@LANG('gen.Title'):</label>
			<input type="text" id="title" name="title" class="form-control" value="{{$record->title}}" onfocus="setFocus($(this), '#accent-chars')" ></input>	
		</div>
				
		<div class="form-group">
			<label for="description" class="control-label">@LANG('gen.Description'):</label>
			<textarea id="description" name="description" class="form-control" onfocus="setFocus($(this), '#accent-chars')" >{{$record->description}}</textarea>
		</div>

		<div class="form-group">
		@component('components.control-dropdown-menu', ['record' => $record, 'prefix' => $prefix, 
			'isAdmin' => $isAdmin, 
			'prompt' => 'Site: ',
			'options' => App\Tools::getSiteIds(),
			'selected_option' => $record->site_id,
			'field_name' => 'site_id',
			'prompt_div' => true,
			'select_class' => 'form-control form-control-sm',
		])@endcomponent
		</div>


		<div class="form-group">
			<label for="display_order" class="control-label">@LANG('content.Display Order'):</label>
			<input type="number"  min="0" max="1000" step="1" name="display_order" class="form-control form-control-100" value="{{$record->display_order}}"></input>	
		</div>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}
		
	</form>
	
</div>

@endsection

