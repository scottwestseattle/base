@if (isAdmin())
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">	
		<li class="nav-item"><a class="nav-link" href="/tests">			
			<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>				
		@isset($record)
			<li class="nav-item"><a class="nav-link" href="/tests/view/{{$record->id}}">			
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
			</a></li>		
			<li class="nav-item"><a class="nav-link" href="/tests/edit/{{$record->id}}">			
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
			</a></li>		
			<li class="nav-item"><a class="nav-link" href="/tests/confirmdelete/{{$record->id}}">			
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
			</a></li>		
		@endisset
    </ul>
  </div>
</nav>	
@endif