@php
    $name = isset($name) ? $name : (isset($tag) ? $tag->name : 'name not set');
    $tagId = isset($tag) ? $tag->id : 0;
    $count = $parms['count'];
    $start = $parms['start'];
    $order = isset($parms['order']) ? $parms['order'] : null;
    $nextStart = $start + $count;
    $lengthLimit = 125;
    $moreButtonUrl = empty($tagId) ? 'favorites-review/' : 'list-tag/' . $tagId;

    //$lists = isset($lists) ? $lists : [];
@endphp
@extends('layouts.app')
@section('title', trans_choice('ui.List', 2))
@section('menu-submenu')@component('tags.menu-submenu')@endcomponent @endsection
@section('content')

<div class="page-nav-buttons">
    <a class="btn btn-success btn-sm btn-nav-top" role="button" href="/favorites">
        @LANG('proj.Back to Lists')<span class="glyphicon glyphicon-button-back-to"></span>
    </a>
    @if (isset($tag))
        <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="/definitions/review/{{$tag->id}}">
            @LANG('ui.Review')&nbsp;<span class="glyphicon glyphicon-eye-open"></span>
        </a>
        <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="/definitions/favorites-review?action=flashcards&count=20&tag={{$tag->id}}">
            @LANG('proj.Flashcards')<span class="ml-1 glyphicon glyphicon-flash"></span>
        </a>
        <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="/definitions/favorites-review?action=reader&count=20&tag={{$tag->id}}">
            @LANG('proj.Reader')<span class="ml-1 glyphicon glyphicon-volume-up"></span>
        </a>
    @else
        <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="/definitions/favorites-review?count=20&action=quiz">
            @LANG('ui.Review')&nbsp;<span class="glyphicon glyphicon-eye-open"></span>
        </a>
        <a class="btn btn-primary btn-sm btn-nav-top" role="button" href="/definitions/favorites-review?count=20&action=flashcards">
            @LANG('proj.Flashcards')<span class="ml-1 glyphicon glyphicon-flash"></span>
        </a>
    @endif
</div>

<h3 name="" class="" style="margin-bottom:10px;">{{$name}}@component('components.badge', ['text' => count($records)])@endcomponent
    @if (isset($tag) && (isAdmin() || App\User::isOwner($tag->user_id)))
        <span style="" class="small-thin-text pl-3 middle"><a href="/definitions/list-tag/{{$tag->id}}?count=50&order=desc">Newest</a></span>
        <span style="" class="small-thin-text pl-3 middle"><a href="/definitions/list-tag/{{$tag->id}}?count=50&order=asc">Oldest</a></span>
        <span style="" class="small-thin-text pl-3 middle">
            @component('components.control-delete-glyph', ['linkText' => 'ui.Remove All', 'href' => '/definitions/remove-favorites/' . $tag->id . '', 'prompt' => 'ui.Confirm Remove All'])@endcomponent
        </span>
    @endif
</h3>


<div id="removeStatus"></div>

<table style="width:100%;" class="table">
    <tbody>
    @foreach($records as $record)
        @php
        $isSnippet = App\Gen\Definition::isSnippetStatic($record);
        @endphp
        <tr id="row{{$record->id}}">
            <td class="icon">
                @component('gen.definitions.component-heart', [
                    'record' => $record,
                    'id' => $record->id,
                    'lists' => $lists,
                    'status' => 'status' . 2 . '-' . $record->id . '',
                ])@endcomponent
            </td>
            <td class="icon">
                <div class="ml-3">
                    @if (isAdmin() || App\User::isOwner($record->user_id))
                        <a href="/{{$isSnippet ? 'practice' : 'definitions'}}/edit/{{$record->id}}">@component('components.icon-edit')@endcomponent</a>
                    @endif
                </div>
            </td>

            <td style="width:100%;">
                @if ($isSnippet)
                    <a href="/definitions/view/{{$record->permalink}}">{{Str::limit($record->title, $lengthLimit)}}</a>
                    <div>
                        @if (isset($record->translation_en))
                            <div class="medium-thin-text" >{{Str::limit($record->translation_en, $lengthLimit)}}</div>
                        @else
                            <a class="small-thin-text red" href="/definitions/edit/{{$record->id}}">@LANG('proj.Add Translation')</a>
                        @endif
                    </div>
                @else
                    <a href="/definitions/view/{{$record->permalink}}">{{$record->title}}</a>
                    <div>
                        @if (isset($record->translation_en))
                            <div class="medium-thin-text" >{{$record->translation_en}}</div>
                        @else
                            <a class="small-thin-text red" href="/definitions/edit/{{$record->id}}">@LANG('proj.Add Translation')</a>
                        @endif
                    </div>
                @endif

                @php
                $attempts = empty($record->qna_attempts) ? 0 : $record->qna_attempts;
                $qna_at = empty($record->qna_at) ? 'never' : $record->qna_at;
                $views = empty($record->views) ? 0 : $record->views;
                @endphp
                @if ($attempts > 0)
                    <div class="small-thin-text" style="">Flashcard Views: {{$attempts}}, Last: {{$qna_at}}</div>
                @else
                    <div class="small-thin-text" style="">Views: {{$views}}, {{(($views > 0) ? 'Last: ' . $record->viewed_at . '' : 'Created: ' . $record->created_at)}}</div>
                @endif

                @if (isAdmin())
                    <div class="small-thin-text" style="">{{$record->updated_at}}</div>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="mb-4"><a class="btn btn-sm btn-success" role="button" href="/definitions/{{$moreButtonUrl}}?start={{$nextStart}}&count={{$count}}&order={{$order}}">@LANG('ui.Show More')</a></div>

@endsection
