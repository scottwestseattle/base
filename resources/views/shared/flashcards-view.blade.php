@if (isset($records))
<table>
    <tbody>
        @foreach($records as $card)
            <tr class="mb-3">
                <td class="pb-4 pr-4" style="vertical-align:top;">{{$loop->index + 1}}) {{$card['q']}}</td>
                <td class="pb-4" style="vertical-align:top;">{{$loop->index + 1}}) {{$card['a']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif
