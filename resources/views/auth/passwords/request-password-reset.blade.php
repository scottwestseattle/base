@extends('layouts.app')
@section('title', 'Request Password Reset Email')
@section('menu-submenu')@component('users.menu-submenu') @endcomponent @endsection
@section('content')
<div class="row justify-content-center form-card-row">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header">@LANG('ui.Reset Password')</div>

			<div class="card-body">
				<form method="POST" action="/password/reset/">
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
					
					<div class="mt-2 text-center text-sm">Please enter the email address on your account and an email with a link to reset your password will be sent.</div>

				</form>
			</div>
		</div>
	</div>
</div>
@endsection