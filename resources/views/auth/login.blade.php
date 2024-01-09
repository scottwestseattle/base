@php
    $locale = app()->getLocale();
@endphp
@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="row justify-content-center form-card-row">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header">@LANG('ui.Login')</div>

			<div class="card-body">
				<form method="POST" action="{{ route('authenticate') }}">
					@csrf

					<div class="form-group row">
						<label for="email" class="col-md-4 col-form-label text-md-right">@LANG('ui.Email Address')</label>

						<div class="col-md-6">
							<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

							@error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>
					</div>

					<div class="form-group row">
						<label for="password" class="col-md-4 col-form-label text-md-right">@LANG('ui.Password')</label>

						<div class="col-md-6">
							<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

							@error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-6 offset-md-4">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

								<label class="form-check-label" for="remember">
									@LANG('ui.Remember Me')
								</label>
							</div>
						</div>
					</div>

					<div class="form-group row mb-0">
						<div class="col-md-8 offset-md-4">
							<button type="submit" class="btn btn-primary">
								@LANG('ui.Login')
							</button>

							<a class="btn btn-link" href="{{route('password.requestReset', ['locale' => $locale])}}">
								@LANG('ui.Forgot Your Password')
							</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
