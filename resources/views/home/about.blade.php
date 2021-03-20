@extends('layouts.app')
@section('title', __('ui.About'))
@section('content')

<h1>{{__('ui.About')}}</h1>

<div class="">
@if (NULL != env('APP_DEBUG'))
	<p>{{domainName()}} v0.0</p>
    @if (isAdmin())
        <p>PHP v{{phpversion()}}</p>
        <p>Laravel v{{ Illuminate\Foundation\Application::VERSION }}</p>
        <p>{{ipAddress()}}</p>
        <p>Hash: {{getVisitorInfo()['hash']}}</p>
    	<p>{{__('ui.Locale')}}: {{App::getLocale()}}</p>
    	<p>Snippet:&nbsp;{{Cookie::get('snippetId')}}</p>
	@endif
@endif

	<p><strong>{{__('base.Info')}}</strong>: {{Config::get('constants.email.info')}}</p>
	<p><strong>{{__('base.Support')}}</strong>: {{Config::get('constants.email.support')}}</p>
	<p><a href="{{lurl('sitemap')}}">{{__('base.Site Map')}}</a></p>

@auth
	<hr />
	<h3>{{trans_choice('base.User', 1)}}</h3>
	<p><strong>{{__('base.Name')}}</strong>: {{Auth::user()->name}}</p>
	<p><strong>{{__('base.Email')}}</strong>: {{Auth::user()->email}}</p>

	@if (isAdmin())
		<p><strong>{{__('base.User Type')}}</strong>: Admin</p>
		<hr />
		<h3>{{__('base.System')}}</h3>
		<p>{{env('APP_NAME')}}</p>
		<p><strong>{{__('base.Server Time')}}</strong>: {{timestamp()}}</p>
		<p><strong>{{__('base.Document Root')}}</strong>: {{base_path()}}</p>
		<p><strong>@LANG('base.App Debug')</strong>: {{env('APP_DEBUG')}}</p>
		<p><strong>{{__('base.Database')}}</strong>: {{env('DB_DATABASE')}}</p>
		<p><strong>{{__('base.Mail Host')}}</strong>: {{env('MAIL_HOST')}}</p>
		<p><strong>{{__('base.Sample Token')}}</strong>: {{uniqueToken()}}</p>
		<p><strong>{{__('base.Singular')}}</strong>: {{trans_choice('ui.Tag', 1)}}</p>
		<p><strong>{{__('base.Plural')}}</strong>: {{trans_choice('ui.Tag', 2)}}</p>
	@endif
@endif

</div>

@endsection
