@extends('layouts.app')
@section('title', __('base.Edit Site'))
@section('menu-submenu')@component('sites.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('base.Edit Site')}}</h1>

	<form method="POST" id="form-edit" action="/sites/update/{{$record->id}}">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('ui.URL'):</label>
			<input type="text" name="title" class="form-control" value="{{$record->title}}"></input>
		</div>

		<div class="form-group">
			<label for="language_flag" class="control-label">{{trans_choice('ui.Language', 1)}}:</label>
            @component('components.control-dropdown-language', [
                'options' => $languages,
                'selected_option' => isset($record->language_flag) ? $record->language_flag : -1,
                'field_name' => 'language_flag',
                'select_class' => 'form-control',
            ])@endcomponent
		</div>

		<div class="form-group">
			<label for="frontpage" class="control-label">@LANG('ui.Front Page'):</label>
			<input type="text" name="frontpage" class="form-control" placeholder="{{__('view.Enter frontpage view file name')}}" value="{{$record->frontpage}}" />
		</div>

		<div class="form-group">
			<label for="description" class="control-label">@LANG('ui.Description'):</label>
			<textarea name="description" class="form-control">{{$record->description}}</textarea>
		</div>

		<div class="form-group">
			<label for="options" class="control-label">{{trans_choice('ui.Option', 2)}}:</label>
			<textarea name="options" class="form-control">{{$record->options}}</textarea>
		</div>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

