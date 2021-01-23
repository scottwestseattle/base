@extends('layouts.app')
@section('title', __('base.Content Management System') )
@section('content')

<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!-- Front Page -->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------->

@php
    $banner = in_array('banner', $options) ? $options['banner'] : null;
    $articles = in_array('articles', $options) ? $options['articles'] : null;
@endphp

<!--------------------------------------------------------------------------------------->
<!-- Banner Photo -->
<!--------------------------------------------------------------------------------------->
@if (isset($banner))
<div style="width:100%; background-color: white; background-position: center; background-repeat: no-repeat; background-image:url('/img/spanish/load-loop.gif'); " >
    <div class="" style="background-image: url(/img/spanish/banners/{{$banner}}); background-size: 100%; background-repeat: no-repeat;">
        <a href="/"><img src="/img/spanish/{{App::getLocale()}}-spacer.png" style="width:100%;" /></a>
    </div>
</div>
@endif
<!--------------------------------------------------------------------------------------->
<!-- Page content -->
<!--------------------------------------------------------------------------------------->
<div>

<!--------------------------------------------------------------------------------------->
<!-- SNIPPETS -->
<!--------------------------------------------------------------------------------------->
@if (false)
@component('shared.snippets', ['options' => $options])@endcomponent
@endif

<!--------------------------------------------------------------------------------------->
<!-- ARTICLES NEW SMALL -->
<!--------------------------------------------------------------------------------------->
@if (isset($articles))
    <h3 class="mt-2">@LANG('view.Latest Articles')</h3>
    <div class="text-center mt-2" style="">
        <div style="display: inline-block; width:100%">
            <table style="width:100%;">
            <?php $count = 0; ?>
            @foreach($articles as $record)

            <tr class="drop-box-ghost-small" style="vertical-align:middle;">
                <td style="min-width:40px; font-size: 14px; padding:5px; color: white; background-color: #74b567; margin-bottom:10px;" >
                    <div style="margin:0; padding:0; line-height:100%;">
                        <div style="font-family:impact; font-size:1.7em; margin:10px 0 10px 0;">{{++$count}}</div>
                    </div>
                </td>
                <td style="color:default; text-align:left; padding:5px 10px;">
                    <table>
                    <tbody>
                        <tr><td style="padding-bottom:5px; font-size: 14px; font-weight:normal;"><a href="/entries/{{$record->permalink}}">{{$record->title}}</a></td></tr>
                        <tr><td style="font-size:.8em; font-weight:100;">
                            <div style="float:left;">
                                @component('components.icon-read', ['href' => "/entries/read/$record->id"])@endcomponent
                                <div style="margin-right:15px; float:left;">{{$record->view_count}} @LANG('ui.views')</div>
                                <div style="margin-right:15px; margin-bottom:5px; float:left;"><a href="/entries/stats/{{$record->id}}">{{str_word_count($record->description)}} {{trans_choice('ui.word', 2)}}</a></div>

                                @if (App\User::isAdmin())
                                    <div style="margin-right:15px; float:left;">
                                        @component('components.control-button-publish', ['record' => $record, 'btnStyle' => 'btn-xxs', 'prefix' => 'entries', 'showPublic' => true])@endcomponent
                                    </div>
                                @endif

                            </div>
                            <div style="float:left;">
                                @if (App\User::isAdmin())
                                <div style="margin-right:5px; float:left;"><a href='/entries/edit/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-edit"></span></a></div>
                                <div style="margin-right:0px; float:left;"><a href='/entries/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphCustom-lt glyphicon glyphicon-trash"></span></a></div>
                                @endif
                            </div>
                        </td></tr>
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

</div>

@endsection
