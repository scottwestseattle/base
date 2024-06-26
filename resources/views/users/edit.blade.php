@extends('layouts.app')
@section('title', 'Edit User')
@section('menu-submenu')@component('users.menu-submenu', ['record' => $user]) @endcomponent @endsection
@section('content')
@php
    $locale = app()->getLocale();
@endphp
<div>
	<form method="POST" action="{{route('users.update', ['locale' => $locale, 'user' => $user->id])}}">

		<div class="form-group">
			<input type="text" name="name" class="form-control" value="{{$user->name }}"></input>
		</div>

		<div class="form-group">
			<input type="text" name="email" class="form-control" value="{{$user->email }}"></input>
		</div>

		@if (isAdmin())
			<div class="form-group">
				<select name="user_type" id="user_type">
					@foreach ($user->getUserTypes() as $key => $value)
						<option value="{{$key}}" {{ $key == $user->user_type ? 'selected' : ''}}>@LANG('ui.' . $value)</option>
					@endforeach
				</select>
			</div>

			<div class="form-group">
				<input type="text" name="password" class="form-control" value="{{$user->password}}"></input>
			</div>
		<div class="form-group">
			<input type="checkbox" name="blocked_flag" id="blocked_flag" class="" value="{{$user->blocked_flag }}" {{ ($user->blocked_flag) ? 'checked' : '' }} />
			<label for="blocked_flag" class="checkbox-label">@LANG('ui.Blocked')</label>
		</div>
		@endif

		<div class="form-group">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>

@stop
