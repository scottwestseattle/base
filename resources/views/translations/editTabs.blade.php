@php
	$cnt = 0;
	$recs = $records['en'];
@endphp
@extends('layouts.app')
@section('title', 'Edit Translations - ' . $filename)
@section('menu-submenu')@component('translations.menu-submenu', ['record' => $filename])@endcomponent @endsection
@section('content')

<div>

	<h1>@LANG('ui.Edit') {{trans_choice('ui.Translation', 1)}}</h1>

	<form method="POST" action="/translations/update/{{$filename}}">

		<div class="form-group">

		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<a class="nav-link active" id="english-tab" data-toggle="tab" href="#english" role="tab" aria-controls="english" aria-selected="true">@LANG('ui.English')</a>
			</li>
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="spanish-tab" data-toggle="tab" href="#spanish" role="tab" aria-controls="spanish" aria-selected="false">@LANG('ui.Spanish')</a>
			</li>
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="chinese-tab" data-toggle="tab" href="#chinese" role="tab" aria-controls="chinese" aria-selected="false">@LANG('ui.Chinese')</a>
			</li>
		</ul>

		<div class="tab-content" id="myTabContent">
			<div class="tab-pane fade show active" id="english" role="tabpanel" aria-labelledby="english-tab">
				<div class="table-responsive w100">
					<table>
						<tr><th></th><th>@LANG('ui.Key')</th><th>{{trans_choice('ui.Translation', 1)}}</th></tr>
					@foreach($recs as $key => $value)
						<tr>
							<td>{{$loop->iteration}}.</td>
							<td style=""><input type="text" name="records[0][{{$loop->iteration}}]" class="form-control" value="{{$key}}"></input></td>
							<td style=""><input type="text" name="records[1][{{$loop->iteration}}]" class="form-control" value="{{$records['en'][$key]}}"></input>			</td>
						<tr>
					@endforeach
					</table>
				</div>
			</div>

			<div class="tab-pane fade" id="spanish" role="tabpanel" aria-labelledby="spanish-tab">
				<div class="table-responsive w100">

					<table>
						<tr><th></th><th>@LANG('ui.Key')</th><th>{{trans_choice('ui.Translation', 1)}}</th></tr>
					@foreach($recs as $key => $value)
						<tr>
							<td>{{$loop->iteration}}.</td>
							<td style=""><input type="text" name="records[0][{{$loop->iteration}}]" class="form-control" value="{{$key}}"></input></td>
							<td style=""><input type="text" name="records[2][{{$loop->iteration}}]" class="form-control" value="{{$records['es'][$key]}}"></input>			</td>
						<tr>
					@endforeach
					</table>

				</div>
			</div>

			<div class="tab-pane fade" id="chinese" role="tabpanel" aria-labelledby="chinese-tab">
				<div class="table-responsive w100">

					<table>
						<tr><th></th><th>@LANG('ui.Key')</th><th>{{trans_choice('ui.Translation', 1)}}</th></tr>
					@foreach($recs as $key => $value)
						<tr>
							<td>{{$loop->iteration}}.</td>
							<td style=""><input type="text" name="records[0][{{$loop->iteration}}]" class="form-control" value="{{$key}}"></input></td>
							<td style=""><input type="text" name="records[3][{{$loop->iteration}}]" class="form-control" value="{{$records['zh'][$key]}}"></input>			</td>
						<tr>
					@endforeach
					</table>

				</div>
			</div>
		</div>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}

	</form>

</div>

@stop
