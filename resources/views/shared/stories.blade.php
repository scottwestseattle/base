@php
    $locale = app()->getLocale();

    $aotdId = isset($options['aotd']->id) ? $options['aotd']->id : 0;

    $colors = [
        'Tomato',
        'MediumSeaGreen',
        'DodgerBlue',
        'Teal',
        'purple',
        'maroon',
        'orange',
        'Violet',
        'SlateBlue',
        'Olive',
        'DarkOrange',
    ];

    $colorIndex = 0;
    $imgPath = public_path() . '/img/backgrounds/covers';
    $recordCount = count($records) - (($aotdId > 0) ? 1 : 0);
@endphp
<div class="container page-normal">

	<h2 class="ml-0">@LANG('proj.More Stories')<span class="title-count">({{$recordCount}})</span></h2>

    <div class="row mb-3">
        @foreach($records as $record)
            @php
                $coverImage = \App\Gen\Article::getCoverImage($record->id, $record->level_flag);
            @endphp
            @if ($record->id != $aotdId)
            <div class="text-center mb-2 ml-2"
            style="min-width:100px; max-width:45%; border-radius:10px; background-color: {{$coverImage ? 'default' : $colors[$colorIndex % 10]}};
                background-image:url('/img/books/pattern.png'); background-size:cover;">
                    <a href="{{route('articles.view', ['locale' => $locale, 'permalink' => $record->permalink])}}">
                    @if (isset($coverImage))
                        <div class="image-wrapper">
                            <a href="{{route('articles.view', ['locale' => $locale, 'permalink' => $record->permalink])}}">
                                <img src="{{$coverImage['url']}}" style="height:230px;" />
                                <span class="image-lable" style="background: {{$coverImage['lableColor']}};">{{$coverImage['lable']}}</span>
                            </a>
                        </div>
                    @else
                        <div style="height:100%; width:155px;">
                            <div style="color: white; padding: 30% 20px; overflow-wrap:break-word; font-weight:bold; font-size:20px;">
                                {{$record->title}}
                            </div>
                        </div>
                        @php $colorIndex++ @endphp
                    @endif
                </a>
            </div>
            @endif
        @endforeach
    </div>
</div>
