@if (isAdmin())
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
		<li class="nav-item"><a class="nav-link" href="/comments">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>
		<li class="nav-item"><a class="nav-link" href="/comments/add">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
		</a></li>
		@isset($record)
			<li class="nav-item"><a class="nav-link" href="/comments/view/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="/comments/edit/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="/comments/publish/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#lightning" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="/comments/confirmdelete/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
			</a></li>
		@else
			<li class="nav-item"><a class="nav-link" href="/comments/deleted">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
			</a></li>
		@endisset
    </ul>
  </div>
</nav>
@endif
