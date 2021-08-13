@section('content')
<xml>
@foreach($records as $record)
	<record>
		<name>{{$record->name}}</name>
		@foreach($record['qna'] as $qna)
			<question language="{{$qna['questionLanguage']}}">{{$qna['q']}}</question>
			<answer language="{{$qna['answerLanguage']}}">{{$qna['a']}}</answer>
		@endforeach
	</record>
@endforeach
</xml>

