@php
@endphp
@extends('layouts.app')
@section('title', __('proj.Verb Conjugation'))
@section('menu-submenu')@component('gen.definitions.menu-submenu', ['prefix' => 'definitions'])@endcomponent @endsection
@section('content')

@component('gen.definitions.component-conjugations-full', ['record' => $record, 'showTitle' => true])@endcomponent

@endsection

