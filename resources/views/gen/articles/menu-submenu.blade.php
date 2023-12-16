<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">

        @if (false)
        <li class="nav-item"><a class="nav-link" href="/articles/index/default/20">
            <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
        </a></li>
        @endif

        @isset($record)
        @else
            <li class="nav-item"><a class="nav-link" href="{{route('articles.index', ['locale' => $locale])}}?sort=date-asc&start=0&count={{DEFAULT_LIST_LIMIT}}">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#sort-numeric-up-alt" /></svg>
            </a></li>
        @endif

        <li class="nav-item"><a class="nav-link" href="{{route('articles.index', ['locale' => $locale])}}?sort=date-desc&start=0&count={{DEFAULT_LIST_LIMIT}}">
            <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#sort-numeric-down-alt" /></svg>
        </a></li>

        @isset($record)
        @else
            <li class="nav-item"><a class="nav-link" href="{{route('articles.index', ['locale' => $locale])}}?sort=title-asc&start=0&count={{DEFAULT_LIST_LIMIT}}">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#sort-alpha-up-alt" /></svg>
            </a></li>

            <li class="nav-item"><a class="nav-link" href="{{route('articles.index', ['locale' => $locale])}}?sort=title-desc&start=0&count={{DEFAULT_LIST_LIMIT}}">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#sort-alpha-down-alt" /></svg>
            </a></li>
        @endif

        @if (false)
        <li class="nav-item"><a class="nav-link" href="{{route('articles.index', ['locale' => $locale])}}/default/-1">
            <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ol" /></svg>
        </a></li>
        @endif

        <li class="nav-item"><a class="nav-link" href="{{route('articles.add', ['locale' => $locale])}}">
            <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
        </a></li>

        @isset($record)
            @if (false)
                <li class="nav-item"><a class="nav-link" href="/articles/view/{{$record->permalink}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
                </a></li>
            @endif

            @if (isset($record) && (\App\User::isOwner($record->user_id) || isAdmin()))
                <li class="nav-item"><a class="nav-link" href="{{route('articles.edit', ['locale' => $locale, 'entry' => $record->id])}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
                </a></li>

                @if (isAdmin())
                    <li class="nav-item"><a class="nav-link" href="{{route('articles.publish', ['locale' => $locale, 'entry' => $record->id])}}">
                        <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#lightning" /></svg>
                    </a></li>
                @endif

                <li class="nav-item"><a class="nav-link" href="{{route('articles.confirmdelete', ['locale' => $locale, 'entry' => $record->id])}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
                </a></li>

                @if (false)
                <li class="nav-item"><a class="nav-link" href="{{route('articles.deleted', ['locale' => $locale])}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
                </a></li>
                @endif

            @endif
        @endif
    </ul>
  </div>
</nav>
