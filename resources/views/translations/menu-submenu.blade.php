@if (isAdmin())
@php
    $locale = app()->getLocale();
@endphp
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
		<li class="nav-item"><a class="nav-link" href="{{route('translations', ['locale' => $locale])}}">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>
		@if (false)
		<li class="nav-item"><a class="nav-link" href="{{route('translations.add', ['locale' => $locale])}}">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
		</a></li>
		@endif
		@isset($record)
			<li class="nav-item"><a class="nav-link" href="{{route('translations.view', ['locale' => $locale, 'filename' => $record])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="{{route('translations.edit', ['locale' => $locale, 'filename' => $record])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
			</a></li>
			@if (false)
			<li class="nav-item"><a class="nav-link" href="{{route('translations.delete', ['locale' => $locale, 'filename' => $record])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
			</a></li>
			@endif
		@endisset
    </ul>
  </div>
</nav>
@endif
