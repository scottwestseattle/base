@if (isAdmin())
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
		<li class="nav-item"><a class="nav-link" href="/home/events">
			<svg class="bi mt-1 text-primary" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>

		<li class="nav-item"><a class="nav-link" href="/home/events/info">
			<svg class="bi mt-1 text-success" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#info-circle" /></svg>
		</a></li>

		<li class="nav-item"><a class="nav-link" href="/home/events/warnings">
			<svg class="bi mt-1 text-warning" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#exclamation-triangle" /></svg>
		</a></li>

		<li class="nav-item"><a class="nav-link" href="/home/events/errors">
			<svg class="bi mt-1 text-danger" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#exclamation-diamond" /></svg>
		</a></li>

		<li class="nav-item"><a class="nav-link" href="/home/events/delete">
			<svg class="bi mt-1 gray" width="22" height="22" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
		</a></li>
    </ul>
  </div>
</nav>
@endif
