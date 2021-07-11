<xml>
@foreach($records as $record)
	<history>
		<history_datetime>{{$record->created_at}}</history_datetime>
		<history_programName>{{$record->program_name}}</history_programName>
		<history_programId>{{$record->program_id}}</history_programId>
		<history_sessionName>{{$record->session_name}}</history_sessionName>
		<history_sessionId>{{$record->session_id}}</history_sessionId>
		<history_seconds>{{$record->seconds}}</history_seconds>
		<history_time>{{secondsToTime($record->seconds)}}</history_time>
	</history>
@endforeach
</xml>
