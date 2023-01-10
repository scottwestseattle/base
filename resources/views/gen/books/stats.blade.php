@extends('layouts.app')
@section('title', __('proj.Stats') . ' - ' . $record->title)
@section('menu-submenu')@component('entries.menu-submenu', ['record' => $record, 'index' => $index])@endcomponent @endsection
@section('content')

<div class="page-size container">

	<div class="mb-5">

		@if (isset($record))
   			<h1 name="title" class=""><a href="{{'/books/chapters/' . $record->id}}">{{$record->name}}</a></h1>
		@endif

		@if (isset($articleCount))
		<h3><b>{{$articleCount}}</b> articles</h3>
		@endif

		<h4>{{__('proj.total words', ['count' => $stats['wordCount']])}}</h4>
		<h4>{{__('proj.unique words', ['count' => $stats['uniqueCount']])}}</h4>

		<div>

		@if ($stats['uniqueCount'] <= 300)

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
			<?php $i = 0; $max = 500; ?>
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

			<h3 class="mt-3">{{__('proj.Most Common Words')}} (200)</h3>
			<?php $i = 0; $max = 2000; //PHP_INT_MAX; ?>
			@foreach($stats['sortCount'] as $key => $value)
				<span><a href="/definitions/find/{{$key}}">{{$key}}</a></span> <span style="font-size:11px; color:gray; margin-right:10px;">({{$value}}) </span>
				<?php if ($i++ > $max) break; ?>
			@endforeach

		@endif

        <div class="mt-3">
            <h3>{{__('proj.Possible Verbs')}} ({{$possibleVerbs}})</h3>

            @foreach($stats['sortCount'] as $key => $value)
                @if (Str::endsWith($key, ['ar', 'er', 'ir']))
                    <span><a href="/definitions/find/{{$key}}">{{$key}}</a></span>&nbsp;<span class="" style="font-size:11px; color:gray; margin-right:10px;">({{$value}}) </span>
                @endif
            @endforeach
        </div>

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
