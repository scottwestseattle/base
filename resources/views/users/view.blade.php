@extends('layouts.app')
@section('title', 'View User Profile')
@section('menu-submenu')@component('users.menu-submenu', ['record' => $user]) @endcomponent @endsection
@section('content')
@php
    $locale = app()->getLocale();
@endphp
<div>
	<h3>{{trans_choice('base.Account', 1)}}</h3>

    @guest

    @else

    @endguest

    @php
        $date = '';
        $date = new DateTime($user->created_at);
        if (isset($date))
            $date = $date->format('F j, Y');
    @endphp

    @if (Auth::check())

        <div class="xl-thin-text">
            <h5>@LANG('ui.Email Address')</h5>
            <p>{{Auth::user()->email}}
            <h5>@LANG('ui.Name')</h5>
            <p>{{Auth::user()->name}}</p>
            <h5>@LANG('ui.Joined')</h5>
            <p>{{$date}}</p>

            @if (isAdmin())
                <h5>@LANG('ui.Type')</h5>
                <p>@LANG('ui.' . $user->getUserType()) ({{$user->user_type}})</p>

                <h5>@LANG('ui.Blocked')</h5>
                <p>@LANG('ui.' . $user->getBlocked())</p>
            @endif
        </div>

    @endif

    <div class="mt-3">
        <a type="button" class="btn btn-primary" href="{{route('users.edit', ['locale' => $locale, 'user' => Auth::id()])}}">@LANG('ui.Edit')</a>
        <a type="button" class="btn btn-primary" href="{{route('password.edit', ['locale' => $locale, 'user' => Auth::id()])}}">@LANG('base.Update Password')</a>
    </div>

</div>

@endsection
