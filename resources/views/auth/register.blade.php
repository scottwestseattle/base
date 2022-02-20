@extends('layouts.app')
@section('title', 'Register New User')
@section('content')
@php
    $sum = intval($rand1) + intval($rand2);
@endphp
<div class="row justify-content-center form-card-row">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header">@LANG('ui.Register')</div>

			<div class="card-body">
				<form method="POST" action="/users/create">
					@csrf

                    <input type="hidden" name="rand1" value="{{$rand1}}" />
                    <input type="hidden" name="rand2" value="{{$rand2}}" />

					<div class="form-group row">
						<label for="name" class="col-md-4 col-form-label text-md-right">@LANG('ui.Name'):</label>
						<div class="col-md-6">
							<input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            <div><label class="small-thin-text">(@LANG('ui.Minimum of :count characters', ['count' => 5]))</div>
							@error('name')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>
					</div>

					<div class="form-group row">
						<label for="email" class="col-md-4 col-form-label text-md-right">@LANG('ui.Email Address'):</label>
						<div class="col-md-6">
							<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
							@error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
                            <div><label class="small-thin-text">(@LANG('ui.Email will be confirmed'))</div>
						</div>
					</div>

					<div class="form-group row">
						<label for="password" class="col-md-4 col-form-label text-md-right">@LANG('ui.Password'):</label>
						<div class="col-md-6">
							<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            <div><label class="small-thin-text">(@LANG('ui.Minimum of :count characters', ['count' => 8]))</div>
							@error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>
					</div>

					<div class="form-group row">
						<label for="password-confirm" class="col-md-4 col-form-label text-md-right">@LANG('ui.Confirm Password'):</label>
						<div class="col-md-6">
							<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
						</div>
					</div>

					<div class="form-group row">
						<label for="sum" class="col-md-4 col-form-label text-md-right">{{$rand1}} plus {{$rand2}}:</label>
						<div class="col-md-6">
							<input id="sum" type="number" class="form-control" name="sum" required>
						</div>
					</div>

					<div class="form-group row mb-0">
						<div class="col-md-6 offset-md-4">
							<button type="submit" class="btn btn-primary">
								@LANG('ui.Register')
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
