@extends('layouts.app')
@section('title', 'View User Profile')
@section('menu-submenu')@component('users.menu-submenu', ['record' => $user]) @endcomponent @endsection
@section('content')
<div>
	<h3>{{trans_choice('ui.Profile', 1)}}</h3>

    @guest

    @else

    @endguest

    @if (isAdmin())

	<table style="font-size:1.2em;">
		<tr><td>@LANG('ui.Name'):</td><td><b>{{$user->name}} ({{ $user->id }})</b></td></tr>
		<tr><td>@LANG('ui.Email'):</td><td><b>{{$user->email}}</b></td></tr>
		<tr><td>@LANG('ui.Type'):</td><td><b>@LANG('ui.' . $user->getUserType()) ({{$user->user_type}})</b></td></tr>
		<tr><td>@LANG('ui.Blocked'):</td><td><b>@LANG('ui.' . $user->getBlocked())</b></td></tr>
		<tr><td>@LANG('ui.Created'):</td><td><b>{{$user->created_at}}</b></td></tr>
		<tr><td>@LANG('ui.Updated'):</td><td><b>{{$user->updated_at}}</b></td></tr>
	</table>

    @elseif (Auth::check())

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

    @else


    @endif

    <div>
        <a href="/logout">@LANG('ui.Log-out')</a>
    </div>

</div>

@endsection
