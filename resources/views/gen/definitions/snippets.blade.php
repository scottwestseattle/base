@php
    $showGlobalSearchBox = false;
@endphp
@extends('layouts.app')
@section('title', __('proj.Practice Text'))
@section('content')

@component('shared.snippets', ['options' => $options, 'history' => $history])@endcomponent

@endsection
