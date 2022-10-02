<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">

        <li class="nav-item"><a class="nav-link" href="/articles/index/default/10">
            <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ol" /></svg>
        </a></li>

        <li class="nav-item"><a class="nav-link" href="/articles/index/date/10">
            <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#sort-numeric-down-alt" /></svg>
        </a></li>

        <li class="nav-item"><a class="nav-link" href="/articles/index/default/-1">
            <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
        </a></li>

        <li class="nav-item"><a class="nav-link" href="/articles/add">
            <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
        </a></li>

        @isset($record)
            <li class="nav-item"><a class="nav-link" href="/articles/view/{{$record->permalink}}">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
            </a></li>

            @if (isset($record) && (\App\User::isOwner($record->user_id) || isAdmin()))
                <li class="nav-item"><a class="nav-link" href="/articles/edit/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
                </a></li>

                @if (isAdmin())
                    <li class="nav-item"><a class="nav-link" href="/articles/publish/{{$record->id}}">
                        <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#lightning" /></svg>
                    </a></li>
                @endif

                <li class="nav-item"><a class="nav-link" href="/articles/confirmdelete/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
                </a></li>

                @if (false)
                <li class="nav-item"><a class="nav-link" href="/articles/deleted">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
                </a></li>
                @endif

            @endif
        @endif
    </ul>
  </div>
</nav>
