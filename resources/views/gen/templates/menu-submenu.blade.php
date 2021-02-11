@if (isAdmin())
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
		<li class="nav-item"><a class="nav-link" href="/templates">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>
		<li class="nav-item"><a class="nav-link" href="/templates/add">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
		</a></li>
		@isset($record)
		    @if (isset($record->permalink))
                <li class="nav-item"><a class="nav-link" href="/templates/view/{{$record->permalink}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
                </a></li>
			@else
                <li class="nav-item"><a class="nav-link" href="/templates/show/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
                </a></li>
			@endif
			<li class="nav-item"><a class="nav-link" href="/templates/edit/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="/templates/publish/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#lightning" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="/templates/confirmdelete/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
			</a></li>
		@else
			<li class="nav-item"><a class="nav-link" href="/templates/deleted">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
			</a></li>
		@endisset
    </ul>
  </div>
</nav>
@endif
