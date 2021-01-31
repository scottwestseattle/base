@extends('layouts.app')
@section('title', __('base.Dashboard'))
@section('content')

	<h1>{{__('base.Dashboard')}}</h1>

	@if (!App\User::isConfirmed())
		<div class="text-center center">
		<h3>You email address has not been confirmed</h3>
		<p><a href="/email/send/{{Auth::id()}}">Click here to resend the confirmation email to {{Auth::user()->email}}</a></p>
		</div>
	@endif

    @if (isAdmin())
		<p class="xl-thin-text">{{domainName()}}</p>
        <table>
			<tr><td><strong>Server Time:</strong>&nbsp;&nbsp;</td><td>{{date("M d, Y H:i:s")}}</td></tr>
			<tr><td><strong>Language:</strong></td><td>{{$language['name']}} ({{$language['short'] . ', ' . $language['long']}})</td></tr>
            <tr><td><strong>Client:</strong></td><td>{{ipAddress()}}&nbsp<span style="font-size:11px;">({{getVisitorInfo()['hash']}})</span></td></tr>
            <tr><td><strong>Folder:</strong></td><td>{{base_path()}}</td></tr>
			<tr><td><strong>Session:</strong></td><td>{{env('SESSION_LIFETIME', 0)}}</td></tr>
    		<tr><td><strong>Debug:</strong></td><td>{{(NULL != env('APP_DEBUG')) ? 'ON' : 'OFF'}}</td></tr>
        </table>

        <div class="mt-2">
            <a href="/" type="button" class="btn btn-sm btn-primary">Debug</a>
            <a href="/" type="button" class="btn btn-sm btn-primary">Geo</a>
            <a href="/hash" type="button" class="btn btn-sm btn-primary">Hash</a>
        </div>

        @php
            $emergency = intval($events['emergency']);
            $errors = intval($events['errors']);
        @endphp
        @if ($errors > 0 || $emergency > 0)
		<hr />
            <h1 class="red">{{trans_choice('ui.Error', 2)}}</h1>

            <div class="{{$emergency > 0 ? 'red' : ''}}">
                <h4>{{__('base.Emergency Events') }} ({{$emergency}})</h4>
            </div>

            <div class="{{$errors > 0 ? 'red' : ''}}">
                <h4>{{__('base.Error Events')}} ({{$errors}})</h4>
            </div>

            <a href="/events">{{__('base.Go to Events')}}</a>
        @endif

		<hr />
        <div class="mt-4">
            <h3>Features</h3>
            <ul>
                <li>Blog</li>
                <li>Blog Entry</li>
                <li>Comments</li>
                <li>Contact Form</li>
                <li>Error Handling</li>
                <li>EU GDPR Compliance Notice</li>
                <li>Form Validation</li>
                <li>Forum</li>
                <li>Geo-enabled</li>
                <li>Icon</li>
                <li>Site Map</li>
                <li>User Access Levels</li>
                <li>Visitor Tracking</li>
                <li><strong><i>Domain-aware (multiple sites)</i></strong></li>
                <li><strong><i>Event Logging</i></strong></li>
                <li><strong><i>Https Certificate</i></strong></li>
                <li><strong><i>Hosting</strong></i></li>
                <li><strong><i>Soft Deletes</i></strong></li>
                <li><strong><i>Templates</i></strong></li>
                <li><strong><i>User Management</i></strong></li>
                <li><strong><i>Terms of Use</i></strong></li>
                <li><strong><i>Localization</i></strong></li>
                <li><strong><i>Privacy Policy</i></strong></li>
            </ul>
        </div>

		<hr />
		<h1>{{trans_choice('base.User', 2)}} ({{$users}})</h1>
		<p><a href="/users">{{__('base.Go to Users')}}</a>
	@endif

@endsection
