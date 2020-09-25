@extends('layouts.app')

@section('content')

@component('components.menu-submenu', ['data' => $data])
	@component('users.menu-submenu')@endcomponent
@endcomponent

<div class="container page-normal">

	<form method="POST" action="/users/update/{{ $user->id }}">

		<div class="form-group">
			<input type="text" name="name" class="form-control" value="{{$user->name }}"></input>
		</div>
		
		<div class="form-group">
			<input type="text" name="email" class="form-control" value="{{$user->email }}"></input>
		</div>
					
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
			<label for="blocked_flag" class="checkbox-big-label">@LANG('ui.Blocked')</label>
		</div>

		<div class="form-group">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Save')</button>
		</div>
	{{ csrf_field() }}
	</form>

</div>

@stop
