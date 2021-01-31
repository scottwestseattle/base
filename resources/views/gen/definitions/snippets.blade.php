@extends('layouts.app')
@section('title', __('proj.Practice Text'))
@section('content')

@component('shared.snippets', ['options' => $options])@endcomponent

@endsection
