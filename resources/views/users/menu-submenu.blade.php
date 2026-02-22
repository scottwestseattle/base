@if (isAdmin())
@php
    $locale = $app->getLocale();
@endphp
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
		<li class="nav-item"><a class="nav-link" href="{{route('users', ['locale' => $locale])}}">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>

		<li class="nav-item"><a class="nav-link" href="{{route('users.create', ['locale' => $locale])}}">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#person-plus" /></svg>
		</a></li>

		@isset($record)
			<li class="nav-item"><a class="nav-link" href="{{route('users.view', ['locale' => $locale, 'user' => $record->id])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="{{route('users.edit', ['locale' => $locale, 'user' => $record->id])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="{{route('users.confirmDelete', ['locale' => $locale, 'user' => $record->id])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
			</a></li>
		@endisset

        <li class="nav-item"><a class="nav-link" href="{{route('users.deleted', ['locale' => $locale])}}">
            <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
        </a></li>
    </ul>
  </div>
</nav>
@endif
