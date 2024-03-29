@extends('layouts.app')
@section('title', __('base.Dashboard'))
@section('content')
@php
    $sessionMinutes = intval(Config::get('session.lifetime'));
    $sessionDays = $sessionMinutes / (60 * 24);
@endphp
	@if (!App\User::isConfirmed())
		<div class="">
		<h3>You email address has not been confirmed</h3>
		<p><a href="/email/send/{{Auth::id()}}">Click here to resend the confirmation email to {{Auth::user()->email}}</a></p>
		</div>
	@endif

    @if (isAdmin())
    	<h1>{{__('base.Dashboard')}}</h1>

		<p class="xl-thin-text">{{domainName()}}</p>
        <table>
			<tr><td><strong>Server Time:</strong>&nbsp;&nbsp;</td><td>{{date("M d, Y H:i:s")}}</td></tr>
			<tr><td><strong>PHP Version:</strong>&nbsp;&nbsp;</td><td>{{phpversion()}}</td></tr>
			<tr><td><strong>Language:</strong></td><td>{{$language['name']}} ({{$language['short'] . ', ' . $language['long']}})</td></tr>
            <tr><td><strong>Client:</strong></td><td>{{ipAddress()}}</td></tr>
			<!-- tr><td><strong>Session:</strong></td><td>{{env('SESSION_LIFETIME', 0)}}</td></tr -->
	        <tr><td><strong>Snippet:</strong></td><td>{{Cookie::get('snippetId')}}</td></tr>
            <tr><td><strong>Folder:</strong></td><td class="xs-text">{{base_path()}}</td></tr>
    		<tr><td><strong><a href="/dashboard" type="button" class="btn btn-sm btn-warning">Debug</a></strong></td><td>{{(NULL != env('APP_DEBUG')) ? 'ON' : 'OFF'}}</td></tr>
            <tr><td><strong><a href="/hash" type="button" class="btn btn-sm btn-primary">Hash</a></strong></td><td class="xs-text">{{getVisitorInfo()['hash']}}</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td><strong>Session Lifetime:&nbsp;&nbsp;</strong></td><td class="">{{$sessionDays}} days ({{$sessionMinutes}} minutes)</td></tr>
            <tr><td><a href="/test" class="">Test</a></td></tr>
            <tr><td><a href="/clear-cache" class="">Clear Cache</a></td></tr>
            <tr><td><a href="/clear-sessions" class="">Clear Sessions</a></td></tr>
            <tr><td><a href="/clear-view" class="">Clear View Cache</a></td></tr>
        </table>

        <hr />

		<h1>{{trans_choice('base.User', 2)}}<span class="title-count">({{$users}})</span></h1>
		@if (intval($users) > 0 && isset($userNewest) /* && $userNewest->isUserConfirmed() */)
		    <p class="medium-thin-text">
		        <b>Newest:&nbsp;</b>{{$userNewest->name}} ({{$userNewest->getUserType()}}), {{$userNewest->email}}, {{translateDate($userNewest->created_at, true)}}
		    </p>
		@endif
		<p><a href="/users">{{__('base.Go to Users')}}</a></p>
        <hr />

        @component('shared.history', ['history' => $history])@endcomponent
        <hr />

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

	@else

        @php
            $date = '';
            $date = new DateTime(Auth::user()->created_at);
            if (isset($date))
                $date = $date->format('F j, Y');
        @endphp
        <div>
            <p class="xl-thin-text">@LANG('ui.Hello')&nbsp;{{Auth::user()->name}}
                <a class="medium-thin-text" href="/users/edit/{{Auth::id()}}">({{strtolower(__('ui.Edit'))}})</a>
            </p>
        </div>
        <hr />

        @component('shared.history', ['history' => $history])@endcomponent

	@endif

@endsection
