@php
    $title = isset($record->title) ? $record->title : __('ui.Not Found');
    $cols1 = 'col-xs-12 col-sm-6 col-md-4 col-lg-3 col-verb-conj';
    $cols2 = 'col-xs-12 col-sm-6 col-md-4 col-lg-3 col-verb-conj';
    $tdClass = 'alignRight medium-thin-text';
    $headers = isset($record->conjugationHeaders) ? $record->conjugationHeaders : [];
    $showTitle = (isset($showTitle) && $showTitle);
@endphp

@if (isset($record->conjugations))

    @if ($showTitle)
        <h3>{{$record->title}}</h3>
    @endif

    <div class="conjugations"><!-- for fonts -->

    <h3 class='large-thin-text'>{{trans_choice('proj.Conjugation', 2)}}</h3>

    <div class="mb-4">
        <h4>{{trans_choice('proj.Participle', 2)}}:</h4>
        <p>{{$record->conjugations[0]}}</p>
    </div>

    <div class="mb-5">
    <h4>{{__('proj.Indicative')}}</h4>
    <div class="row">
    @foreach($record->conjugations['tenses'] as $r)
    @if ($loop->index >= 0 && $loop->index < 5)
    <div class="{{$cols1}}"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
        <div class="card card-verb-conj mt-4">
            <h5 class="mb-3">{{__($headers[$loop->index + 1])}}</h5>
            <table>
            @foreach($r as $key => $value)
                <tr><td class="{{$tdClass}}"><i>{{$value['pronoun']}}</i></td><td style="width:10px;"></td><td>{{$value['conj']}}</td></tr>
            @endforeach
            </table>
        </div>
    </div>
    @endif
    @endforeach
    </div>
    </div>

    <div class="mb-5">
    <h4>{{__('proj.Subjunctive')}}</h4>
    <div class="row">
    @foreach($record->conjugations['tenses'] as $r)
    @if ($loop->index >= 5 && $loop->index < 9)
    <div class="{{$cols2}}"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
        <div class="card card-verb-conj mt-4">
            <h5 class="mb-3">{{__($headers[$loop->index + 1])}}</h5>
            <table>
            @foreach($r as $key => $value)
                <tr><td class="{{$tdClass}}"><i>{{$value['pronoun']}}</i></td><td style="width:10px;"></td><td>{{$value['conj']}}</td></tr>
            @endforeach
            </table>
        </div>
    </div>
    @endif
    @endforeach
    </div>
    </div>

    <div class="mb-5">
    <h4>{{__('proj.Imperative')}}</h4>
    <div class="row">
    @foreach($record->conjugations['tenses'] as $r)
    @if ($loop->index >= 9)
    <div class="{{$cols2}}"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
        <div class="card card-verb-conj mt-4">
            <h5 class="mb-3">{{__($headers[$loop->index + 1])}}</h5>
            <table>
            @foreach($r as $key => $value)
                <tr><td class="{{$tdClass}}"><i>{{$value['pronoun']}}</i></td><td style="width:10px;"></td><td>{{$value['conj']}}</td></tr>
            @endforeach
            </table>
        </div>
    </div>
    @endif
    @endforeach
    </div>
    </div>

    </div><!-- for fonts -->

@else

    <h3>{{__($title)}}</h3>
    <div class="mt-2">
        <h4>@LANG('proj.Verb not found')</h4>
    </div>

@endif
