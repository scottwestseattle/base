@php
    $score = empty($record->qna_score) ? 0 : $record->qna_score;
    $attempts = empty($record->qna_attempts) ? 0 : $record->qna_attempts;
    $views = empty($record->views) ? 0 : $record->views;
    $reads = empty($record->reads) ? 0 : $record->reads;

    $created_at = 'Created: ' . App\DateTimeEx::getShortDate($record->created_at);
    $qna_at = empty($record->qna_at) ? '' : 'Quiz: ' . App\DateTimeEx::getShortDate($record->qna_at);
    $viewed_at = empty($record->viewed_at) ? '' : 'Viewed: ' . App\DateTimeEx::getShortDate($record->viewed_at);
    $read_at = empty($record->read_at) ? '' : 'Read: ' . App\DateTimeEx::getShortDate($record->read_at);
    $class = 'ml-0 badge-dark badge-green badge-small';
    $hideZeros = isset($hideZeros) ? $hideZeros : false;

    // show or hide zeros
    $attempts = ($hideZeros && $attempts === 0) ? '' : 'Quiz: ' . $attempts;
    $score =    ($hideZeros && $score === 0)    ? '' : 'Score: ' . round($score * 100.0, 1) . '%';
    $views =    ($hideZeros && $views === 0)    ? '' : 'Views: ' . $views;
    $reads =    ($hideZeros && $reads === 0)    ? '' : 'Reads: ' . $reads;
    $style = empty($style) ? 'clear:both;' : $style;
    $div = true || isset($div) && $div;
@endphp

@if ($div)
<div style="{{$style}}" class="mt-2">
    <div class="small-thin-text steelblue">
@endif
        @component('components.badge', ['class' => $class, 'text' => $attempts])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => $score])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => $views])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => $reads])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => $qna_at])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => $viewed_at])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => $read_at])@endcomponent
@if ($div)
    </div>
</div>
@endif
