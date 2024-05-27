@if (isAdmin())
@php
    $locale = app()->getLocale();
@endphp
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
		@isset($record)
            <li class="nav-item"><a class="nav-link" href="/lessons/start/{{$record->id}}">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
            </a></li>
            <li class="nav-item"><a class="nav-link" href="/lessons/add/{{$record->parent_id}}">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
            </a></li>
		    @if (false && isset($record->permalink))
                <li class="nav-item"><a class="nav-link" href="{{route('lessons.view', ['locale' => $locale, 'lesson' => $permalink])}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
                </a></li>
			@else
                <li class="nav-item"><a class="nav-link" href="{{route('lessons.view', ['locale' => $locale, 'lesson' => $record->id])}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
                </a></li>
			@endif
			<li class="nav-item"><a class="nav-link" href="{{route('lessons.edit', ['locale' => $locale, 'lesson' => $record->id])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="{{route('lessons.publish', ['locale' => $locale, 'lesson' => $record->id])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#lightning" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="{{route('lessons.confirmDelete', ['locale' => $locale, 'lesson' => $record->id])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
			</a></li>
            <li class="nav-item"><a class="nav-link" href="{{route('lessons.rssReader', ['locale' => $locale, 'lesson' => $record->id])}}">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#rss" /></svg>
            </a></li>
		@else
			<li class="nav-item"><a class="nav-link" href="{{route('lessons.deleted', ['locale' => $locale])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
			</a></li>
		@endisset
    </ul>
  </div>
</nav>
@endif
