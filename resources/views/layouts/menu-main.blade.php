<nav class="navbar navbar-expand-lg navbar-dark fixed-top app-color-primary">
  <a class="navbar-brand" href="/">
	<svg class="bi mt-1" width="32" height="32" fill="currentColor" >
		<use xlink:href="/img/bootstrap-icons.svg#brightness-high" />
	</svg>
</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">	
	@auth
		<li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>	  
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			{{Auth::user()->name}}
			</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdown">
			@if (is_admin())			
				<a class="dropdown-item" href="/home/events">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#exclamation-diamond" />
						</svg>
					</div>
					<div class="middle ml-1">Events</div>
				</a>
				<a class="dropdown-item" href="{{route('translations')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#chat-right-text" />
						</svg>
					</div>
					<div class="middle ml-1">Translations</div>
				</a>
				<a class="dropdown-item" href="/users">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#people" />
						</svg>
					</div>
					<div class="middle ml-1">Users</div>
				</a>
			@else
				<a class="dropdown-item" href="/users/view/{{Auth::id()}}">Profile</a>
				<a class="dropdown-item" href="/passwords/reset/{{Auth::id()}}">Change Password</a>
			@endif
				<div class="dropdown-divider"></div>
				<a class="dropdown-item red" href="{{ route('logout') }}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#person" />
						</svg>
					</div>
					<div class="middle ml-1">Logout</div>
				</a>
			</div>
		</li>		
	@else
		<li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>	  
		<li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>	  
	@endif
		<li class="nav-item"><a class="nav-link" href="/about">About</a></li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn my-2 my-sm-0 white" type="submit">Search</button>
    </form>
  </div>
</nav>
