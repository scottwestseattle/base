<nav class="navbar navbar-expand-lg navbar-dark fixed-top app-color-primary">
	<a class="navbar-brand" href="/">
		<div class="brand logo middle">
			<svg class="bi mt-1" width="32" height="32" fill="currentColor" >
				<use xlink:href="/img/bootstrap-icons.svg#brightness-high" />
			</svg>
		</div>
	</a>

	<div class="mr-auto navbar-icon-shortcuts">
@auth
		<a class="navbar-item" href="{{route('dashboard')}}">
			<div>
				<svg class="" width="24" height="24" fill="currentColor" style="{{isAdmin() ? 'color:gold;' : ''}}" >
					<use xlink:href="/img/bootstrap-icons.svg#person-circle" />
				</svg>
			</div>
		</a>
@else
		<a class="navbar-item" href="{{route('login')}}">
			<div>
				<svg class="" width="24" height="24" style="color:LightGray;" >
					<!-- use xlink:href="/img/bootstrap-icons.svg#box-arrow-in-right" / -->
					<use xlink:href="/img/bootstrap-icons.svg#person-circle" />
				</svg>
			</div>
		</a>
@endauth

		<a class="navbar-item" href="/">
			<div>
				<svg class="" width="20" height="20" fill="currentColor" >
					<use xlink:href="/img/bootstrap-icons.svg#search" />
				</svg>
			</div>
		</a>	
		
		<!-- Language Selector Dropdown -->
		<div class="mt-1 middle dropdown">
			<a href="#" class="navbar-item" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
				<img width="25" src="/img/language-{{App::getLocale()}}.png" />
			</a>
			<ul style="float: left; background-color:transparent; border:0;"  class="dropdown-menu">
				<li><a href="/language/en"><img src="/img/language-en.png" /></a></li>
				<li><a href="/language/es"><img src="/img/language-es.png" /></a></li>
				<li><a href="/language/zh"><img src="/img/language-zh.png" /></a></li>
			</ul>
		</div>		
	</div>

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">	
	@auth
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			{{Auth::user()->name}}
			</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdown">
			@if (isAdmin())			
				<a class="dropdown-item" href="/events">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#exclamation-diamond" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('base.Event', 2)}}</div>
				</a>
				<a class="dropdown-item" href="{{route('translations')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#chat-right-text" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('base.Translation', 2)}}</div>
				</a>
				<a class="dropdown-item" href="/users">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#people" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('base.User', 2)}}</div>
				</a>
				<div class="dropdown-divider"></div>
			@endif
				<a class="dropdown-item" href="/users/view/{{Auth::id()}}">
					<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
						<use xlink:href="/img/bootstrap-icons.svg#person" />
					</svg>
					<div class="middle ml-1">{{__('base.Profile')}}</div>
				</a>
				<a class="dropdown-item" href="/password/edit/{{Auth::id()}}">
					<svg class="float-left bi mt-1" width="20" height="20" fill="currentColor" >
						<use xlink:href="/img/bootstrap-icons.svg#pencil-square" />
					</svg>
					<div class="middle ml-2">{{__('base.Password')}}</div>
				</a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item red" href="{{ route('logout') }}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#box-arrow-right" />
						</svg>
					</div>
					<div class="middle ml-1">{{__('base.Log-out')}}</div>
				</a>
			</div>
		</li>		
	@else
		<li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{__('base.Log-in')}}</a></li>	  
		<li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{__('base.Register')}}</a></li>
	@endif
	
	@if (isAdmin())
		<li class="nav-item"><a class="nav-link" href="/mvc">{{__('base.MVC')}}</a></li>
	@endif
		<li class="nav-item"><a class="nav-link" href="/about">{{__('base.About')}}</a></li>
    </ul>
    <form class="form-inline my-2 my-lg-0 d-none d-lg-block"><!-- only for large so it doesn't clutter the dropdown -->
      <input class="form-control mr-sm-2" type="search" placeholder="{{__('base.Search')}}" aria-label="Search">
      <button class="btn my-2 my-sm-0 white" type="submit">{{__('base.Search')}}</button>
    </form>
  </div>
</nav>


