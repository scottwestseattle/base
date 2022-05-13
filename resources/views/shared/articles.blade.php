@if (isset($records))
<div class="text-center mt-2">
    <div style="display: inline-block; width:100%">
        <table style="width:100%;">
        <?php $count = 0; ?>
        @foreach($records as $record)

        <tr class="drop-box-ghost-small" style="vertical-align:middle;">
            <td style="color:default; text-align:left; padding:5px 10px;">
                <table>
                <tbody>
                    <tr>
                        <td style="padding-bottom:5px; font-size: 14px; font-weight:normal;"><a href="/articles/view/{{$record->permalink}}">{{$record->title}}</a></td>
                    </tr>
                    <tr>
                        <td class="small-thin-text">
                            <div class="float-left">{{App\DateTimeEx::getShortDateTime($record->display_date, 'M d, Y')}}</div>
                            <div class="float-left ml-3">
                                <a class="btn btn-primary btn-xs" role="button" href="/articles/read/{{$record->id}}">
                                    @LANG('proj.Reader')<span class="glyphicon glyphicon-volume-up ml-1"></span>
                                </a>
                            </div>
                            @if (App\Entry::hasTranslationStatic($record))
                            <div class="float-left ml-2">
                                <a class="btn btn-success btn-xs" role="button" href="/articles/flashcards/{{$record->id}}">
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

                            <div style="margin-right:10px; font-size:10px; margin-top:5px; float:left;">{{$record->view_count}} {{trans_choice('ui.view', 2)}}</div>
                            <div style="margin-right:10px; font-size:10px; margin-top:5px; float:left;"><a href="/entries/stats/{{$record->id}}">{{str_word_count($record->description)}} {{strtolower(trans_choice('ui.Word', 2))}}</a></div>

                            @if (isAdmin() || App\User::isOwner($record->user_id))
                                @if (isAdmin())
                                <div style="margin-right:10px; float:left;">
                                    @component('components.control-button-publish', ['record' => $record, 'btnStyle' => 'btn-xxs', 'prefix' => 'articles', 'showPublic' => true, 'ajax' => true, 'reload' => true])@endcomponent
                                </div>
                                @endif
                                <div style="margin-right:5px; float:left;"><a href='/articles/edit/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-edit"></span></a></div>
                                <div style="margin-right:0px; float:left;"><a href='/articles/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-trash"></span></a></div>
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
        <div class="mb-4"><a class="btn btn-sm btn-success" role="button" href="/articles">@LANG('ui.Show All')</a></div>
    </div>
</div>
@endif
