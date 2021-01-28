@extends('layouts.app')
@section('title', trans_choice('Article', 2) )
@section('menu-submenu')@component('entries.menu-submenu', ['index' => 'articles'])@endcomponent @endsection
@section('content')

@component('shared.articles', ['options' => $options])@endcomponent

@endsection
