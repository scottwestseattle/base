@if (isAdmin())
@php
    $index = isset($index) ? $index : 'entries';
@endphp
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
		<li class="nav-item"><a class="nav-link" href="/{{$index}}">
			<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>
		<li class="nav-item"><a class="nav-link" href="/entries/add">
			<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
		</a></li>
		@isset($record)
			<li class="nav-item"><a class="nav-link" href="/entries/view/{{$record->id}}">
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="/entries/edit/{{$record->id}}">
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="/entries/publish/{{$record->id}}">
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#lightning" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="/entries/confirmdelete/{{$record->id}}">
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
			</a></li>
		@else
			<li class="nav-item"><a class="nav-link" href="/entries/deleted">
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
			</a></li>
		@endisset
    </ul>
  </div>
</nav>
@endif
