@php
    $orderBy = isset($orderBy) ? $orderBy : 'default';
    $count = isset($options['count']) ? $options['count'] : DEFAULT_LIST_LIMIT;
    $start = isset($options['start']) ? $options['start'] + $count : 0;
    $showingAll = count($records) == 0 && true; //count($records) < $count;
    $class = 'ml-0 badge-dark badge-green badge-small';
    $style = 'margin-right:3px; font-size:10px; margin-top:5px; float:left;';
    $locale = app()->getLocale();
    $imgPath = public_path() . '/img/backgrounds/covers';
    $imgUrl = 'https://codespace.us/img/backgrounds/covers';
    $imgHeight = '70px';
@endphp
@if (isset($records))
<div class="text-center mt-2">
    <div style="display: inline-block; width:100%">
        <table style="width:100%;">
        @foreach($records as $record)
        <tr class="drop-box-ghost-small" style="vertical-align:middle;">
            <td style="color:default; text-align:left; padding:0; width:10px;">
                @php $photo = file_exists($imgPath . '/' . $record->id . '.png'); @endphp
                <a href="{{route('articles.view', ['locale' => $locale, 'permalink' => $record->permalink])}}">
                    @if ($photo)
                        <img style="height:{{$imgHeight}};" src="{{$imgUrl}}/{{$record->id}}.png" />
                    @else
                        <img style="height:{{$imgHeight}};" src="{{$imgUrl}}/periodico.png" />
                    @endif
                </a>
            </td>
            <td style="color:default; text-align:left; padding:5px 10px;">
                <table>
                <tbody>
                    <tr>
                        <td style="padding-bottom:5px; font-size: 14px; font-weight:normal;"><a href="{{route('articles.view', ['locale' => $locale, 'permalink' => $record->permalink])}}">{{$record->title}}</a></td>
                    </tr>
                    <tr>
                        <td class="small-thin-text">
                            <div class="float-left">{{App\DateTimeEx::getShortDateTime($record->display_date, 'M d, Y', false)}}</div>
                            <div class="float-left ml-3">
                                <a class="btn btn-primary btn-xs" role="button" href="{{route('articles.read', ['locale' => $locale, 'entry' => $record->id])}}">
                                    @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                                </a>
                            </div>
                            @if (App\Entry::hasTranslationStatic($record))
                            <div class="float-left ml-2">
                                <a class="btn btn-success btn-xs" role="button" href="{{route('articles.flashcards', ['locale' => $locale, 'entry' => $record->id])}}">
                                    @LANG('proj.Flashcards')<span class="glyphicon glyphicon-flash ml-1"></span>
                                </a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:100;">
                            <div class="float-left mr-3">
                                <img width="25" src="/img/flags/{{getSpeechLanguage($record->language_flag)['code']}}.png" />
                            </div>
                            <div class="" style="{{$style}}">@component('components.badge', ['class' => $class, 'text' => $record->view_count . ' ' . trans_choice('ui.view', 2)])@endcomponent</div>
                            <div class="" style="{{$style}}"><a href="/entries/stats/{{$record->id}}">@component('components.badge', ['class' => $class, 'text' => str_word_count($record->description) . ' ' . strtolower(trans_choice('ui.Word', 2))])@endcomponent</div></a>
                            <div class="" style="{{$style}}">@component('components.badge', ['class' => $class, 'text' => countLetters($record->description) . ' ' . strtolower(trans_choice('ui.Letter', 2))])@endcomponent</div>

                            @if (isAdmin() || App\User::isOwner($record->user_id))
                                @if (isAdmin())
                                <div style="margin-right:10px; float:left;">
                                    @component('components.control-button-publish', ['record' => $record, 'btnStyle' => 'btn-xxs', 'prefix' => 'articles', 'showPublic' => true, 'ajax' => true, 'reload' => true])@endcomponent
                                </div>
                                @endif
                                <div style="margin-right:5px; float:left;"><a href='{{route('articles.edit', ['locale' => $locale, 'entry' => $record->id])}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-edit"></span></a></div>
                                <div style="margin-right:0px; float:left;">@component('components.control-delete-glyph', ['svg' => 'trash', 'href' => route('articles.delete', ['locale' => $locale, 'entry' => $record->id]), 'prompt' => 'ui.Confirm Delete'])@endcomponent</div>
                            @endif
                        </td>
                    </tr>
                    @if (isAdmin() && !App\User::isOwner($record->user_id))
                    <tr>
                        <td>
                            <div class="small-thin-text">@LANG('ui.Member'): {{$record->user_id}}</div>
                        </td>
                    </tr>
                    @endif
                </tbody>
                </table>
            </td>
        </tr>

        <tr style="" class=""><td colspan="2"><div style="height:15px;">&nbsp;</div></td></tr>

        @endforeach
        </table>
        @if (!$showingAll)
            <div class="mb-4"><a class="btn btn-sm btn-success" role="button" href="{{route('articles.index', ['locale' => $locale])}}?sort={{$orderBy}}&start={{$start}}&count={{$count}}">@LANG('ui.Show More')</a></div>
        @endif
    </div>
</div>
@endif
