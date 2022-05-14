@php
    $owner = (isAdmin() || (isset($record) && App\User::isOwner($record->user_id)));
@endphp
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
    @if (isAdmin())
		<li class="nav-item"><a class="nav-link" href="/tags">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>
		@if (false) /* add is by tag function */
            <li class="nav-item"><a class="nav-link" href="/tags/add">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
            </a></li>
        @endif
    @endif
	@isset($record)
	    @if (isAdmin())
			<li class="nav-item"><a class="nav-link" href="/tags/view/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
			</a></li>
			<li class="nav-item"><a class="nav-link" href="/tags/edit/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
			</a></li>
			@if (false) /* not used yet */
                <li class="nav-item"><a class="nav-link" href="/tags/publish/{{$record->id}}">
                    <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#lightning" /></svg>
                </a></li>
			@endif
		@endif
		@if ($owner)
			<li class="nav-item"><a class="nav-link" href="/tags/confirmdelete/{{$record->id}}">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
			</a></li>
		@endif
	@else
	    @if (isAdmin())
			<li class="nav-item"><a class="nav-link" href="/tags/deleted">
				<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
			</a></li>
		@endif
	@endisset
	    @if (isAdmin())
            <li class="nav-item"><a class="nav-link" href="/favorites/rss">
                <svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#rss" /></svg>
            </a></li>
        @endif
    </ul>
  </div>
</nav>
