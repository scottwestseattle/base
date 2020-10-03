@extends('layouts.app')
@section('title', 'Request Password Reset Email')
@section('content')
<div class="row justify-content-center form-card-row">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header">@LANG('base.Reset Password')</div>

			<div class="card-body">
				<form method="POST" action="/password/send-password-reset">
					@csrf

					<div class="form-group row">
															
						<label for="email" class="col-md-4 col-form-label text-md-right">@LANG('base.Email Address')</label>

						<div class="col-md-6">
							<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

							@error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror							
						<button type="submit" class="mt-3 btn btn-primary">
							@LANG('base.Send Password Reset Link')
						</button>
						</div>
							
					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>
@endsection