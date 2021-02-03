@extends('layouts.app')
@section('title', __('proj.Edit Definition'))
@section('menu-submenu')@component('gen.definitions.menu-submenu', ['record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

	<h1>{{__('proj.Edit Definition')}}</h1>

    @component('gen.definitions.component-heart', [
        'record' => $record,
        'lists' => $favoriteLists,
    ])@endcomponent

	<form method="POST" id="form-edit" action="/definitions/update/{{$record->id}}">

		<div class="form-group">
			<label for="title" class="control-label">@LANG('ui.Title'):</label>
			<input type="text" name="title" class="form-control" value="{{$record->title}}"></input>
		</div>

		<div class="form-group">
			<label for="definition" class="control-label">{{trans_choice('proj.Definition', 1)}}:</label>
			<textarea name="definition" class="form-control">{{$record->description}}</textarea>
		</div>


		<div class="form-group">
			<label for="translation_en" class="control-label">{{trans_choice('proj.Translation', 2)}}:</label>
			<textarea name="translation_en" class="form-control">{{$record->translation_en}}</textarea>
		</div>

		<div class="form-group">
			<label for="examples" class="control-label">{{trans_choice('proj.Example', 2)}}:</label>
			<textarea name="examples" class="form-control">{{$record->examples}}</textarea>
		</div>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>
@endsection

