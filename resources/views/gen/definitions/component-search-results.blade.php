@php
    $locale = app()->getLocale();
@endphp
<div class="col-sm"><!-- need to split word list into multiple columns here -->
	<div class="table" style="font-size: 13px;">
		<table id="searchDefinitionsResultsTable" class="table-responsive table-striped table-condensed" style="min-height:500px;">
			<tbody>
				@foreach($records as $record)
				<tr>
					<td class="large-text hidden-xs">
						<a class="float-left" href="{{route('definitions.view', ['locale' => $locale, 'permalink' => $record->permalink])}}">{{$record->title}}</a>
						@component('gen.definitions.component-search-toolbar', ['record' => $record, 'id' => 1, 'lists' => $favoriteLists])@endcomponent
					</td>
					<td>
						<div class="large-text hidden-lg hidden-md hidden-sm">
							<a class="float-left" href="{{route('definitions.view', ['locale' => $locale, 'permalink' => $record->permalink])}}">{{$record->title}}</a>
							@component('gen.definitions.component-search-toolbar', ['record' => $record, 'id' => 2, 'lists' => $favoriteLists])@endcomponent
						</div>

                        @if (isset($record->pos_flag))
							<div class="small-thin-text">{{__(strtolower($record->getPos()))}}{{isAdmin() ? ' (' . $record->id . ')' : ''}}</div>
						@elseif (isAdmin())
							<div class="small-thin-text">@LANG('ui.Not Set')&nbsp;({{$record->id}})</div>
                        @endif

						@if (isset($record->definition))
							<div class="medium-thin-text mb-2">{!!nl2br($record->definition)!!}</div>
						@elseif (App\User::isSuperAdmin())
							<a href="{{route('definitions.edit', ['locale' => $locale, 'definition' => $record->id])}}" class="small-thin-text danger">add definition</a>
						@endif

						<div class="teal"><i>{!!nl2br($record->examples)!!}</i></div>

						@if (isset($record->translation_en))
							<div class="mt-2 steelblue">{{__('base.English')}}: {!!nl2br($record->translation_en)!!}</div>
						@elseif (App\User::isSuperAdmin())
							<a href="{{route('definitions.edit', ['locale' => $locale, 'definition' => $record->id])}}" class="small-thin-text danger">add translation</a>
						@endif

						<div class="small-thin-text mt-2">
							{{$record->view_count}} view{{$record->view_count !== 1 ? 's' : ''}}@if (isset($record->last_viewed_at))<span>,  last on {{timestamp2date($record->last_viewed_at)}}</span>@endif
						</div>

						@if (isset($record->conjugations))
							<div class="small-thin-text mt-2"><a href="" onclick="event.preventDefault(); $('#showconjugations-{{$record->id}}').toggle(); ajaxexec('/definitions/conjugationscomponent/{{$record->id}}', '#showconjugations-{{$record->id}}');">
								@if (App\Gen\Spanish::fixConjugations($record))
									<a href="{{route('definitions.edit', ['locale' => $locale, 'definition' => $record->id])}}" class="small-thin-text danger">conjugations</a>
								@else
									<span>{{trans_choice('proj.Conjugation', 2)}}</span>
								@endif
							</a></div>
							<div id="showconjugations-{{$record->id}}" class="hidden"></div>
							@if (false)
							    <a href="/verbs/{{$record->title}}">Full Conjugation</a>
							@endif
						@endif

					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
<!-- end of repeat block -->
