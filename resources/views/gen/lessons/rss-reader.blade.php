@section('content')
<xml>
@foreach($records as $record)
	<record>
		<name>{{$record->title}}</name>
		@foreach($record['qna'] as $qna)
			<question>{{$qna['q']}}</question>
			<answer>{{$qna['a']}}</answer>
		@endforeach
	</record>
@endforeach
</xml>
