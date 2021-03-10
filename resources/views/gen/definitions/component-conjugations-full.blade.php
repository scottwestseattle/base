@php
    $title = isset($record->title) ? $record->title : __('ui.Not Found');
    $cols = 'col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 col-verb-conj';
    $tdClass = 'alignRight medium-thin-text';
@endphp

@if (isset($record->conjugations))
    <h3>{{$title}}</h3>

    <h4>{{trans_choice('proj.Participle', 2)}}:</h4>
    <p>{{$record->conjugations[0]}}</p>

    <div class="mb-5">
    <h4>{{__('proj.Indicative')}}</h4>
    <div class="row">
    @foreach($record->conjugations['tenses'] as $r)
    @if ($loop->index >= 0 && $loop->index < 5)
    <div class="{{$cols}}"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
        <div class="card card-verb-conj mt-4">
            <h4 class="mb-3">{{$headers[$loop->index + 1]}}</h4>
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
    <div class="{{$cols}}"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
        <div class="card card-verb-conj mt-4">
            <h4 class="mb-3">{{$headers[$loop->index + 1]}}</h4>
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
    <div class="{{$cols}}"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
        <div class="card card-verb-conj mt-4">
            <h4 class="mb-3">{{$headers[$loop->index + 1]}}</h4>
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

@else

    <h3>{{$title}}</h3>
    <div class="mt-2">
        <h4>@LANG('proj.Verb not found')</h4>
    </div>

@endif
