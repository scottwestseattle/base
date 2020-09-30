@if (is_admin())
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">	
		<li class="nav-item"><a class="nav-link" href="/events">
			<svg class="bi mt-1 text-primary" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>
		
		<li class="nav-item"><a class="nav-link" href="/events/index/info">			
			<svg class="bi mt-1 text-success" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#info-circle" /></svg>
		</a></li>
		
		<li class="nav-item"><a class="nav-link" href="/events/index/warnings">
			<svg class="bi mt-1 text-warning" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#exclamation-triangle" /></svg>		
		</a></li>	 
		
		<li class="nav-item"><a class="nav-link" href="/events/index/errors">
			<svg class="bi mt-1 text-danger" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#exclamation-diamond" /></svg>				
		</a></li>	  		
		
		<li class="nav-item"><a class="nav-link" href="/events/confirmdelete">
			<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>				
		</a></li>	  		
    </ul>
  </div>
</nav>	
@endif