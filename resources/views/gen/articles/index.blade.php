@extends('layouts.app')
@section('title', trans_choice('proj.Article', 2) )
@section('menu-submenu')@component('gen.articles.menu-submenu', ['prefix' => 'articles'])@endcomponent @endsection
@section('content')

@component('shared.articles', ['options' => $options])@endcomponent

@endsection
