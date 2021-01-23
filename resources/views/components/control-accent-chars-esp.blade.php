<?php
	$id = isset($target) ? $target : 0;
	$tinymce = isset($tinymce);
	$flat = isset($flat) && $flat;
	$hidden = $tinymce ? '' : 'hidden';
?>

@if ($flat)

	<div id="accent-chars" style="clear:both;" class="data-accent-chars flat-content mb-1 {{$hidden}}">
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('á', '{{$id}}', '{{$tinymce}}')">á</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('é', '{{$id}}', '{{$tinymce}}')">é</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('í', '{{$id}}', '{{$tinymce}}')">í</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('ñ', '{{$id}}', '{{$tinymce}}')">ñ</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('ó', '{{$id}}', '{{$tinymce}}')">ó</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('ú', '{{$id}}', '{{$tinymce}}')">ú</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('ü', '{{$id}}', '{{$tinymce}}')">ü</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('Á', '{{$id}}', '{{$tinymce}}')">Á</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('É', '{{$id}}', '{{$tinymce}}')">É</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('Í', '{{$id}}', '{{$tinymce}}')">Í</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('Ñ', '{{$id}}', '{{$tinymce}}')">Ñ</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('Ó', '{{$id}}', '{{$tinymce}}')">Ó</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('Ú', '{{$id}}', '{{$tinymce}}')">Ú</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('Ü', '{{$id}}', '{{$tinymce}}')">Ü</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('¿', '{{$id}}', '{{$tinymce}}')">¿</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('¡', '{{$id}}', '{{$tinymce}}')">¡</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('&rarr;', '{{$id}}', '{{$tinymce}}')">&rarr;</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('&#xab;', '{{$id}}', '{{$tinymce}}')">&#xab;</button>
		<button tabindex="-1" onclick="event.preventDefault(); insertChar('&#xbb;', '{{$id}}', '{{$tinymce}}')">&#xbb;</button>

	</div>

@else

<div class="data-accent-chars ml-2">
	<div id="accent-chars" class="dropdown">
		<button id="dropdown-trigger-button" onclick="event.preventDefault();">ñ</button>
		<div class="dropdown-content accent-buttons">
			<button onclick="event.preventDefault(); insertChar('á', '{{$id}}', '{{$tinymce}}')">á</button>
			<button onclick="event.preventDefault(); insertChar('é', '{{$id}}', '{{$tinymce}}')">é</button>
			<button onclick="event.preventDefault(); insertChar('í', '{{$id}}', '{{$tinymce}}')">í</button>
			<button onclick="event.preventDefault(); insertChar('ó', '{{$id}}', '{{$tinymce}}')">ó</button>
			<button onclick="event.preventDefault(); insertChar('ú', '{{$id}}', '{{$tinymce}}')">ú</button>
			<button onclick="event.preventDefault(); insertChar('ü', '{{$id}}', '{{$tinymce}}')">ü</button>
			<button onclick="event.preventDefault(); insertChar('ñ', '{{$id}}', '{{$tinymce}}')">ñ</button>
			<button onclick="event.preventDefault(); insertChar('Á', '{{$id}}', '{{$tinymce}}')">Á</button>
			<button onclick="event.preventDefault(); insertChar('É', '{{$id}}', '{{$tinymce}}')">É</button>
			<button onclick="event.preventDefault(); insertChar('Í', '{{$id}}', '{{$tinymce}}')">Í</button>
			<button onclick="event.preventDefault(); insertChar('Ó', '{{$id}}', '{{$tinymce}}')">Ó</button>
			<button onclick="event.preventDefault(); insertChar('Ú', '{{$id}}', '{{$tinymce}}')">Ú</button>
			<button onclick="event.preventDefault(); insertChar('Ü', '{{$id}}', '{{$tinymce}}')">Ü</button>
			<button onclick="event.preventDefault(); insertChar('Ñ', '{{$id}}', '{{$tinymce}}')">Ñ</button>
			<button onclick="event.preventDefault(); insertChar('¿', '{{$id}}', '{{$tinymce}}')">¿</button>
			<button onclick="event.preventDefault(); insertChar('¡', '{{$id}}', '{{$tinymce}}')">¡</button>
			<button onclick="event.preventDefault(); insertChar('&#xab;', '{{$id}}', '{{$tinymce}}')">&#xab;</button>
			<button onclick="event.preventDefault(); insertChar('&#xbb;', '{{$id}}', '{{$tinymce}}')">&#xbb;</button>
		</div>
	</div>
</div>

@endif
