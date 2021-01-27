@extends('layouts.app')

@section('content')

<div class="container page-normal">

@component('shared.snippets', ['options' => $options])@endcomponent

</div>

@endsection
