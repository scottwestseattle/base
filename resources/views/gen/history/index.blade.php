@extends('layouts.app')
@section('title', trans_choice('ui.History', 2))
@section('menu-submenu')@component('gen.history.menu-submenu')@endcomponent @endsection
@section('content')
<div>
    @component('shared.history', ['history' => $records])@endcomponent
</div>

@endsection
