<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
        @if (isAdmin())
            <li class="nav-item"><a class="nav-link" href="/dictionary">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
            </a></li>
            <li class="nav-item"><a class="nav-link" href="/definitions">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#gear" /></svg>
            </a></li>
            <li class="nav-item"><a class="nav-link" href="/definitions/add">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
            </a></li>
        @endif
		@isset($record)
		    @if (false)
                <li class="nav-item"><a class="nav-link" href="/definitions/show/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
                </a></li>
			@endif
            @if (isAdmin() || App\User::isOwner($record->user_id))
                <li class="nav-item"><a class="nav-link" href="/definitions/edit/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
                </a></li>
            @endif
            @if (isAdmin())
                <li class="nav-item"><a class="nav-link" href="/definitions/publish/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#lightning" /></svg>
                </a></li>
            @endif
            @if (isAdmin() || App\User::isOwner($record->user_id))
                <li class="nav-item"><a class="nav-link" href="/definitions/confirmdelete/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
                </a></li>
            @endif
		@else
            @if (isAdmin())
                <li class="nav-item"><a class="nav-link" href="/definitions/deleted">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
                </a></li>
            @endif
		@endisset
    </ul>
  </div>
</nav>
