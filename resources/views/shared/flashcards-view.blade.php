@if (isset($records))
<table>
    <tbody>
        @foreach($records as $card)
            <tr class="mb-3">
                <td class="pb-4 pr-4" style="vertical-align:top; width:50%;"><span class="mr-2 fn">{{$loop->index + 1}}</span>{{$card['q']}}</td>
                <td class="pb-4" style="vertical-align:top;"><span class="mr-2 fn">{{$loop->index + 1}}</span>{{$card['a']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif
