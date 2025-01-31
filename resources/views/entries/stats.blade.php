@extends('layouts.app')
@section('title', __('proj.Stats') . ' - ' . $record->title)
@section('menu-submenu')@component('entries.menu-submenu', ['record' => $record, 'index' => $index])@endcomponent @endsection
@section('content')
@php
    $locale = app()->getLocale();
@endphp
<div class="page-size container">

	<div class="mb-5">

		@if (isset($record))
   			<h2 name="title" class=""><a href="{{$record->getViewLink()}}">{{$record->title}}</a></h2>
		@endif

		@if (isset($articleCount))
		<h3><b>{{$articleCount}}</b> articles</h3>
		@endif

		<h4>{{__('proj.total words', ['count' => $stats['wordCount']])}}</h4>
		<h4>{{__('proj.unique words', ['count' => $stats['uniqueCount']])}}</h4>

		<div>

		@if (false && $stats['uniqueCount'] <= 300)

			<h3 class="mt-3">{{__('proj.Most Commonly Used')}}</h3>
			@foreach($stats['sortCount'] as $key => $value)
				<span><a href="/definitions/find/{{$key}}">{{$key}}</a></span> <span style="font-size:11px; color:gray; margin-right:10px;">({{$value}}) </span>
			@endforeach

			<h3 class="mt-3">{{__('proj.Alphabetical Order')}}</h3>
			@foreach($stats['sortAlpha'] as $key => $value)
				<span><a href="/definitions/find/{{$key}}">{{$key}}</a></span> <span style="font-size:11px; color:gray; margin-right:10px;">({{$value}}) </span>
			@endforeach

		@elseif (isset($articleCount))

			<h3 class="mt-3">{{__('proj.Most Common Words')}}</h3>
			<?php $i = 0; $max = 50000; ?>
			@foreach($stats['sortCount'] as $key => $value)
				<span><a href="/definitions/find/{{$key}}">{{$key}}</a></span> <span style="font-size:11px; color:gray; margin-right:10px;">({{$value}}) </span>
				<?php if ($i++ > $max) break; ?>
			@endforeach

			<?php $i = 0; ?>
			<h3 class="mt-3">{{__('proj.Alphabetical Order')}}</h3>
			@foreach($stats['sortAlpha'] as $key => $value)
				<span><a href="/definitions/find/{{$key}}">{{$key}}</a></span> <span style="font-size:11px; color:gray; margin-right:10px;">({{$value}}) </span>
				<?php if ($i++ > $max) break; ?>
			@endforeach

		@else

			<h3 class="mt-3">{{__('proj.Ordered by Number of uses')}}</h3>
			<?php $i = 0; $max = 20000; ?>
			@foreach($stats['sortCount'] as $key => $value)
				<span><a href="/definitions/find/{{$key}}">{{$key}}</a></span> <span style="font-size:11px; color:gray; margin-right:10px;">({{$value}}) </span>
				<?php if ($i++ > $max) break; ?>
			@endforeach

			<?php $i = 0; ?>
			<h3 class="mt-3">{{__('proj.Alphabetical Order')}}</h3>
			@foreach($stats['sortAlpha'] as $key => $value)
				<span><a href="/definitions/find/{{$key}}">{{$key}}</a></span>
				<?php if ($i++ > $max) break; ?>
			@endforeach
		@endif

@if (false)
        <div class="mt-3">
            <h3>{{__('proj.Possible Verbs')}} ({{$possibleVerbs}})</h3>

            @foreach($stats['sortCount'] as $key => $value)
                @if (Str::endsWith($key, ['ar', 'er', 'ir']))
                    @php
                        $value = trim($value);
                    @endphp
                    @if (!empty($value))
                        <span><a href="/definitions/find/{{$key}}">{{$key}}</a></span>&nbsp;<span class="" style="font-size:11px; color:gray; margin-right:10px;">({{$value}}) </span>
                    @else
                        <div>TRIMMED TO BLANK</div>
                    @endif
                @endif
            @endforeach
        </div>
@endif

		</div>

		@if (false)
		<h3>A</h3>
		<div>
		@foreach($stats['sortAlpha'] as $key => $value)
			@if (App\Tools::startsWith($key, 'a'))
				{{$key . ' (' . $value . ') '}}
			@endif
		@endforeach
		</div>

		<h3>B</h3>
		<div>
		@foreach($stats['sortAlpha'] as $key => $value)
			@if (App\Tools::startsWith($key, 'b'))
				{{$key . ' (' . $value . ') '}}
			@endif
		@endforeach
		</div>
		@endif
	</div>

</div>

@endsection
