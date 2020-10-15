@if (isAdmin())
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">	
		<li class="nav-item"><a class="nav-link" href="/templates">			
			<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#list-ul" /></svg>
		</a></li>				
		<li class="nav-item"><a class="nav-link" href="/templates/add">			
			<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#file-earmark-plus" /></svg>
		</a></li>		
		@isset($record)
			<li class="nav-item"><a class="nav-link" href="/templates/view/{{$record->id}}">			
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#eye" /></svg>
			</a></li>		
			<li class="nav-item"><a class="nav-link" href="/templates/edit/{{$record->id}}">			
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#pencil-square" /></svg>
			</a></li>		
			<li class="nav-item"><a class="nav-link" href="/templates/confirmdelete/{{$record->id}}">			
				<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#trash" /></svg>
			</a></li>		
		@endisset
		<li class="nav-item"><a class="nav-link" href="/templates/deleted">			
			<svg class="bi mt-1 gray" width="24" height="24" ><use xlink:href="/img/bootstrap-icons.svg#bootstrap-reboot" /></svg>
		</a></li>		
    </ul>
  </div>
</nav>	
@endif