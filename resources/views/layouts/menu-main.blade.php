<nav class="navbar navbar-expand-lg navbar-dark fixed-top app-color-primary">
	<a class="navbar-brand" href="/">
		<div class="brand logo middle">
		    @if (Str::endsWith(domainName(), '.com'))
                <img src="/img/logos/logo-{{domainName()}}.png" style="max-width:40px;"/>
		    @else
			<svg class="bi mt-1" width="32" height="32" fill="currentColor" >
				<use xlink:href="/img/bootstrap-icons.svg#{{getLogo()}}" />
			</svg>
			@endif
		</div>
	</a>

	<div class="mr-auto navbar-icon-shortcuts">
@auth
		<a class="navbar-item" href="{{lurl('dashboard')}}">
			<div>
				<svg class="" width="24" height="24" fill="currentColor" style="{{isAdmin() ? 'color:gold;' : ''}}" >
					<use xlink:href="/img/bootstrap-icons.svg#person-circle" />
				</svg>
			</div>
		</a>
@else
		<a class="navbar-item" href="{{lurl('login')}}">
			<div>
				<svg class="" width="24" height="24" fill="currentColor" style="color:LightGray;" >
					<use xlink:href="/img/bootstrap-icons.svg#person-circle" />
				</svg>
			</div>
		</a>
@endauth

		<a class="navbar-item" href="/search" onclick="event.preventDefault(); $('#popup-search').toggle(); $('#popup-search-text').focus();">
			<div>
				<svg class="" width="20" height="20" fill="currentColor" >
					<use xlink:href="/img/bootstrap-icons.svg#search" />
				</svg>
			</div>
		</a>

<!--------------------------------------------------------------------------------------->
<!-- The Search form -->
<!--------------------------------------------------------------------------------------->
@php
    $search = isset($search) ? $search : null;
    $records = isset($records) ? $records : [];
@endphp

	<div id="popup-search" class="popup-search hidden">
        <form method="POST" action="/definitions/add" autocomplete="off">

            <input value="" name="title" id="popup-search-text" type="search"
                class="form-control form-control-sm form-control-inline py-2 xborder-right-0 border"
                placeholder="{{__('proj.Dictionary Search')}}"
                oninput="showSearchResult(this.value, false, 'popup-search-results'); $('#popup-search-options').show()"
            />

            <div class="" style="height: 10px;"></div>

            <div id="popup-search-options" class="mb-1 hidden float-left mr-1">
                <button id="popup-search-button-articles" type="button" class="btn btn-info btn-xs"
                onclick="showSearchResult($('#popup-search-text').val(), true, 'popup-search-results'); $('#popup-search-options').hide();"
                >Search Articles/Books</button>
            </div>
            <button onclick="event.preventDefault(); $('#popup-search').hide();" class="btn btn-success btn-xs">Close</button>

            <div id="popup-search-results" class=""></div>
            {{ csrf_field() }}
        </form>
	</div>
<!--------------------------------------------------------------------------------------->
<!-- End of Search form -->
<!--------------------------------------------------------------------------------------->

@if (false) // doesn't fit
		<a class="navbar-item" href="/practice">
			<div>
				<svg class="" width="20" height="20" fill="currentColor" >
					<use xlink:href="/img/bootstrap-icons.svg#list-check" />
				</svg>
			</div>
		</a>
@endif
		<a class="navbar-item" href="/favorites">
			<div>
				<svg class="" width="20" height="20" fill="currentColor" >
					<use xlink:href="/img/bootstrap-icons.svg#heart-fill" />
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
        @if (isAdmin() || \App\Site::site()->hasOption('articles'))
		    <li class="nav-item"><a class="nav-link" href="{{lurl('articles')}}">{{trans_choice('proj.Article', 2)}}</a></li>
		@endif
        @if (isAdmin() || \App\Site::site()->hasOption('books'))
    		<li class="nav-item"><a class="nav-link" href="{{lurl('books')}}">{{trans_choice('proj.Book', 2)}}</a></li>
		@endif
        @if (isAdmin() || \App\Site::site()->hasOption('dictionary'))
	    	<li class="nav-item"><a class="nav-link" href="{{lurl('dictionary')}}">{{__('proj.Dictionary')}}</a></li>
		@endif
        @if (isAdmin() || \App\Site::site()->hasOption('lists'))
		    <li class="nav-item"><a class="nav-link" href="{{lurl('favorites')}}">{{trans_choice('ui.List', 2)}}</a></li>
        @endif
        @if (isAdmin() || \App\Site::site()->hasOption('courses'))
    		<li class="nav-item"><a class="nav-link" href="{{lurl('courses')}}">{{trans_choice('proj.Course', 2)}}</a></li>
    	@endif
	@auth
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			    {{Auth::user()->name}}
			</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdown">
			@if (isAdmin())
				<a class="dropdown-item" href="{{lurl('comments')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#chat-right-text" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('ui.Comment', 2)}}</div>
				</a>

				<a class="dropdown-item" href="{{lurl('entries')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#collection" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('ui.Entry', 2)}}</div>
				</a>

				<a class="dropdown-item" href="{{lurl('events')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#exclamation-diamond" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('ui.Event', 2)}}</div>
				</a>

				<a class="dropdown-item" href="{{lurl('lessons')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#book" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('proj.Lesson', 2)}}</div>
				</a>

				<a class="dropdown-item" href="{{lurl('mvc')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#code-slash" />
						</svg>
					</div>
					<div class="middle ml-1">{{__('MVC')}}</div>
				</a>

				<a class="dropdown-item" href="{{lurl('templates')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#diagram-3" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('ui.Template', 2)}}</div>
				</a>
				<a class="dropdown-item" href="{{lurl('sites')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#display" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('ui.Site', 2)}}</div>
				</a>
				<a class="dropdown-item" href="{{lurl('tags')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#tags" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('ui.Tag', 2)}}</div>
				</a>
				<a class="dropdown-item" href="{{lurl('translations')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#globe" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('ui.Translation', 2)}}</div>
				</a>
				<a class="dropdown-item" href="{{lurl('users')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#people" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('ui.User', 2)}}</div>
				</a>
				<a class="dropdown-item" href="{{lurl('visitors')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#people-fill" />
						</svg>
					</div>
					<div class="middle ml-1">{{trans_choice('ui.Visitor', 2)}}</div>
				</a>
				<div class="dropdown-divider"></div>
			@endif
				<a class="dropdown-item" href="{{lurl('users/view/' . Auth::id())}}">
					<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
						<use xlink:href="/img/bootstrap-icons.svg#person" />
					</svg>
					<div class="middle ml-1">{{trans_choice('base.Account', 1)}}</div>
				</a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item red" href="{{lurl('logout')}}">
					<div class="middle">
						<svg class="float-left bi mt-1" width="24" height="24" fill="currentColor" >
							<use xlink:href="/img/bootstrap-icons.svg#box-arrow-right" />
						</svg>
					</div>
					<div class="middle ml-1">{{__('ui.Log-out')}}</div>
				</a>
			</div>
		</li>
	@else
		<li class="nav-item"><a class="nav-link" href="{{lurl('login')}}">{{__('base.Log-in')}}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{lurl('register')}}">{{__('base.Register')}}</a></li>
	@endif
		<li class="nav-item"><a class="nav-link" href="{{lurl('about')}}">{{__('ui.About')}}</a></li>
    </ul>
    @if (false)
    <form class="form-inline my-2 my-lg-0 d-none d-lg-block"><!-- only for large so it doesn't clutter the dropdown -->
      <input class="form-control mr-sm-2" type="search" placeholder="{{__('base.Search')}}" aria-label="Search">
      <button class="btn my-2 my-sm-0 white" type="submit">{{__('base.Search')}}</button>
    </form>
    @endif
  </div>
</nav>


