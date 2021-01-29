@if (isAdmin())
@php
    $index = isset($index) ? $index : 'entries';
@endphp
<nav class="navbar navbar-light navbar-submenu" style="background-color: #e3f2fd;">
  <div class="">
    <ul class="nav">
		<li class="nav-item">
		    @component('components.icon-link', ['href' => '/' . $index, 'svg' => 'list-ul'])@endcomponent
		</li>
		<li class="nav-item">
			@component('components.icon-link', ['href' => '/entries/add', 'svg' => 'file-earmark-plus'])@endcomponent
		</li>
		@isset($record)
			<li class="nav-item">
       			@component('components.icon-link', ['href' => '/entries/view/' . $record->id, 'svg' => 'eye'])@endcomponent
			</li>
			<li class="nav-item">
    			@component('components.icon-link', ['href' => '/entries/edit/' . $record->id, 'svg' => 'pencil-square'])@endcomponent
			</li>
			<li class="nav-item">
    			@component('components.icon-link', ['href' => '/entries/publish/' . $record->id, 'svg' => 'lightning'])@endcomponent
			</li>
			<li class="nav-item">
    			@component('components.icon-link', ['href' => '/entries/confirmdelete/' . $record->id, 'svg' => 'trash'])@endcomponent
			</li>
		@else
			<li class="nav-item">
    			@component('components.icon-link', ['href' => '/entries/deleted', 'svg' => 'bootstrap-reboot'])@endcomponent
			</li>
		@endisset
    </ul>
  </div>
</nav>
@endif
