<courses>
@foreach($records as $record)
	<course>
		<course_name>{{$record->title}}</course_name>
		<course_description>{{$record->description}}</course_description>
		<course_id>{{$record->id}}</course_id>
		@foreach($record['sessions'] as $key => $lesson)
	<lesson>
				<lesson_name>{{$lesson['title']}}</lesson_name>
				<lesson_description>{{$lesson['description']}}</lesson_description>
				<lesson_id>{{$lesson['id']}}</lesson_id>
				<lesson_number>{{$lesson['number']}}</lesson_number>
				<lesson_parent>{{$lesson['course']}}</lesson_parent>
				<lesson_type>{{$lesson['type']}}</lesson_type>
			</lesson>
		@endforeach
</course>
@endforeach
</courses>
