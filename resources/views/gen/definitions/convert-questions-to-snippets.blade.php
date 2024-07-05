@php
    $translation = isset($parms['translation']) ? $parms['translation'] : null;
    $locale = app()->getLocale();
@endphp
@extends('layouts.app')
@section('title', __('proj.Convert Text to Favorites'))
@section('menu-submenu')@component('gen.articles.menu-submenu', ['locale' => $locale, 'record' => $record])@endcomponent @endsection
@section('content')
<div class="container page-normal">

    <h1>{{__('proj.Convert Questions to Snippets')}}</h1>

	<form method="POST" action="{{route('definitions.convertQuestionsToSnippetsPost', ['locale' => $locale, 'entry' => $record->id])}}">

		<h4>{{$record->title}}</h4>

		<div class="entry-title-div mb-3">
            <label class="tiny">@LANG('ui.Favorites List Name'):</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ $record->title }}" placeholder="Favorites List Name" />
        </div>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Convert')</button>
		</div>

        <div class="entry-div" style="margin-top:20px; width:100%; font-size:1.1em;">
            <div class="entry" style="width:100%;">
                @if (isset($translation))
                    <span id="translation" name="translation" class="">
                        <table>
                            <tbody>
                                @foreach($translation as $card)
                                    <tr class="mb-3">
                                        <td class="pb-4 pr-4" style="vertical-align:top; width:50%;"><span class="mr-2 fn">{{$loop->index + 1}}</span>{{$card['q']}}
                                        @if (!empty($card['choices']))
                                            <div style="font-size:.8em;">{{$card['choices']}}</div>
                                        @endif
                                        </td>
                                        <td class="pb-4" style="vertical-align:top;"><span class="mr-2 fn">{{$loop->index + 1}}</span>{{$card['translation_en']}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </span>
                @endif
            </div>
        </div>

		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Convert')</button>
		</div>

        <div class="entry-div" style="margin-top:20px; width:100%; font-size:1.1em;">
            <div class="entry" style="width:100%;">
                @if (isset($translation))
                    <span id="translation" name="translation" class="">
                        @foreach($translation as $card)
                            @php
                                $color = 'default';
                                $count = 0;
                                if (isset($card['exists']))
                                {
                                    $count = count($card['exists']);
                                    if ($count > 1)
                                    {
                                        $color = 'orange';
                                    }
                                    else
                                    {
                                        $color = 'red';
                                    }
                                }
                            @endphp
                            @if (isset($card['exists']))
                                @foreach($card['exists'] as $id)
                                <div class="pb-4 pr-4" style="">
                                    <a style="color:{{$color}}" href="/es/definitions/edit/{{$id}}" target="_blank">{{$card['q']}}</a>
                                </div>
                                @endforeach
                            @else
                            <div class="pb-4 pr-4" style="">
                                {{$card['q']}}
                            </div>
                            @endif
                        @endforeach
                    </span>
                @endif
            </div>
        </div>
        <input type="hidden" id="language_flag" value="{{$record->language_flag}}" />

	{{ csrf_field() }}
	</form>
</div>
@endsection
