@if (isAdmin())
@php
    $prefix = 'books';
    $bookId = isset($bookId) ? $bookId : null;
    $locale = app()->getLocale();
    $addKey = isset($bookId) ? 'add-chapter/' . $bookId : 'add';
    $hrefAdd = isset($bookId)
        ? route("books.addChapter", ['locale' => $locale, 'tag' => $bookId])
        : route("books.add", ['locale' => $locale]);
@endphp
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
		<li class="nav-item"><a class="nav-link" href="{{route('books.index', ['locale' => $locale])}}">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>
		<li class="nav-item"><a class="nav-link" href="{{$hrefAdd}}">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
		</a></li>
		@isset($record)
			<li class="nav-item"><a class="nav-link" href="/{{$prefix}}/view/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="{{route('books.edit', ['locale' => $locale, 'entry' => $record->id])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="{{route('books.publish', ['locale' => $locale, 'entry' => $record->id])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#lightning" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="{{route('books.confirmDelete', ['locale' => $locale, 'entry' => $record->id])}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
			</a></li>
		@else
			<li class="nav-item"><a class="nav-link" href="/books/deleted">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
			</a></li>
		@endisset
    </ul>
  </div>
</nav>
@endif
