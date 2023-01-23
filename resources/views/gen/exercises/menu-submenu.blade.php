<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
        <li class="nav-item"><a class="nav-link" href="/exercises">
            <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
        </a></li>
		<li class="nav-item"><a class="nav-link" href="/exercises/add">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
		</a></li>
		@isset($record)
            @if (App\User::isAdminOrOwner($record->user_id))
                <li class="nav-item"><a class="nav-link" href="/exercises/show/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
                </a></li>
                <li class="nav-item"><a class="nav-link" href="/exercises/edit/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
                </a></li>
                <li class="nav-item"><a class="nav-link" href="/exercises/confirmdelete/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
                </a></li>
			@endif
		@else
			<li class="nav-item"><a class="nav-link" href="/exercises/deleted">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
			</a></li>
		@endisset
    </ul>
  </div>
</nav>
