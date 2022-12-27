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
@endphp

<div style="clear:both;" class="mt-2">
    <div class="small-thin-text steelblue">
        @component('components.badge', ['class' => $class, 'text' => 'Quiz: ' . $attempts])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => 'Score: ' . round($score * 100.0, 1) . '%'])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => 'Views: ' . $views])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => 'Reads: ' . $reads])@endcomponent
    </div>
    <div class="small-thin-text steelblue" style="">
        @component('components.badge', ['class' => $class, 'text' => $created_at])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => $qna_at])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => $viewed_at])@endcomponent
        @component('components.badge', ['class' => $class, 'text' => $read_at])@endcomponent
    </div>
</div>
