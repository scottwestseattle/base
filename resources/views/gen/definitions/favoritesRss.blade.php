<lists>
@foreach($records as $record)
	    <list>
            <list_name>{{$record->name}}</list_name>
            <list_count>{{count($record->definitions)}}</list_count>
            <list_id>{{$record->id}}</list_id>
    		@foreach($record->definitions as $def)
	        <definition>
                <definition_id>{{$def->id}}</definition_id>
                <definition_type>{{$def->type_flag}}</definition_type>
	            @if ($def->isSnippet())
                    <definition_title>{{$def->title}}</definition_title>
				@else
                    <definition_title>{{$def->title}}</definition_title>
                    <definition_definition>{{$def->definition}}</definition_definition>
				@endif
                <definition_translation>{{$def->translation_en}}</definition_translation>
			</definition>
	    	@endforeach
</list>
@endforeach
</lists>
