@extends('layouts.app')
@section('title', __('base.Dashboard'))
@section('content')

	<h1>{{__('base.Dashboard')}}</h1>

	@if (!App\User::isConfirmed())
		<div class="">
		<h3>You email address has not been confirmed</h3>
		<p><a href="/email/send/{{Auth::id()}}">Click here to resend the confirmation email to {{Auth::user()->email}}</a></p>
		</div>
	@endif

    @if (isAdmin())
		<p class="xl-thin-text">{{domainName()}}</p>
        <table>
			<tr><td><strong>Server Time:</strong>&nbsp;&nbsp;</td><td>{{date("M d, Y H:i:s")}}</td></tr>
			<tr><td><strong>Language:</strong></td><td>{{$language['name']}} ({{$language['short'] . ', ' . $language['long']}})</td></tr>
            <tr><td><strong>Client:</strong></td><td>{{ipAddress()}}</td></tr>
			<!-- tr><td><strong>Session:</strong></td><td>{{env('SESSION_LIFETIME', 0)}}</td></tr -->
	        <tr><td><strong>Snippet:</strong></td><td>{{Cookie::get('snippetId')}}</td></tr>
            <tr><td><strong>Folder:</strong></td><td class="xs-text">{{base_path()}}</td></tr>
    		<tr><td><strong><a href="/dashboard" type="button" class="btn btn-sm btn-warning">Debug</a></strong></td><td>{{(NULL != env('APP_DEBUG')) ? 'ON' : 'OFF'}}</td></tr>
            <tr><td><strong><a href="/hash" type="button" class="btn btn-sm btn-primary">Hash</a></strong></td><td class="xs-text">{{getVisitorInfo()['hash']}}</td></tr>
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

        <div>
            <h1 class="">{{__('History')}}<span class="title-count">(5)</span></h1>
            <div class="">
            <table>
            @foreach ($history as $record)
                <tr>
                    <td class="pr-3">{{$record->program_name}}</td>
                    <td>{{$record->session_name}}</td>
                </tr>
            @endforeach
            </table>
    		<p class="mt-2"><a href="/history">{{__('ui.Show All')}}</a></p>
            </div>
        </div>
        <hr />

        <div>
            <h1 class="">{{__('RSS Links')}}</h1>

            <div class="">
                <div><a target="_blank" href="/courses/rss">Courses</a></div>
                <div><a target="_blank" href="/lessons/rss">Lessons</a></div>
                <div><a target="_blank" href="/history/rss">History</a></div>
            </div>
        </div>
        <hr />

        <div>
            <h1 class="">{{__('Ajax Links')}}</h1>

            <div class="">
                <div><a target="_blank" href="/definitions/scrape-definition/tener">Scrape Definition</a></div>
                <div><a target="_blank" href="/definitions/conjugationsgenajax/tener">Scrape Conjugation</a></div>
                <div><a target="_blank" href="/definitions/find/tener">Find Word</a></div>
                <div><a target="_blank" href="/history/add-public/course-name/0/lesson-name/0/100">Add History</a></div>
            </div>
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

	@else

        @php
            $date = '';
            $date = new DateTime(Auth::user()->created_at);
            if (isset($date))
                $date = $date->format('F j, Y');
        @endphp
        <div class="xl-thin-text">
            <h5>@LANG('ui.Name')</h5>
            <p>{{Auth::user()->name}}
            <h5>@LANG('ui.Email Address')</h5>
            <p>{{Auth::user()->email}}
            <h5>@LANG('ui.Joined')</h5>
            <p>{{$date}}</p>
            <p>
        </div>

        <hr />
        <div>
            <h1 class="">{{__('History')}}</h1>
            <div class="">
                <div><a href="/history">History</a></div>
            </div>
        </div>


	@endif

@endsection
