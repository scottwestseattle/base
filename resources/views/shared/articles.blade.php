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
                        <td class="small-thin-text">{{App\DateTimeEx::getShortDateTime($record->display_date, 'M d, Y')}}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:100;">
                            <div class="float-left mr-3">
                                <img width="25" src="/img/flags/{{getSpeechLanguage($record->language_flag)['code']}}.png" />
                            </div>

                            @component('components.icon-read', ['href' => "/articles/read/$record->id", 'color' => ''])@endcomponent

                            <div style="margin-right:10px; font-size:10px; margin-top:5px; float:left;">{{$record->view_count}} {{trans_choice('ui.view', 2)}}</div>
                            <div style="margin-right:10px; font-size:10px; margin-top:5px; float:left;"><a href="/entries/stats/{{$record->id}}">{{str_word_count($record->description)}} {{strtolower(trans_choice('ui.Word', 2))}}</a></div>

                            @if (isAdmin() || App\User::isOwner($record->user_id))
                                <div style="margin-right:10px; float:left;">
                                    @component('components.control-button-publish', ['record' => $record, 'btnStyle' => 'btn-xxs', 'prefix' => 'articles', 'showPublic' => true])@endcomponent
                                </div>
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
