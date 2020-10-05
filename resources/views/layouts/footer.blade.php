<!-- FOOTER -->
<footer class="footer backin-black">
	<div class="container text-center p-5">
		<a class="" href="/">
			<div class="brand logo middle">
				<svg class="bi app-color-primary-reverse" width="55" height="55" >
					<use xlink:href="/img/bootstrap-icons.svg#brightness-high" />
				</svg>
			</div>
		</a>

		<p style="font-size:2em;" class="footer-heading">{{domainName()}}</p>
		<p style="font-size:1.2em;" class="">{{env('APP_NAME', 'App Name')}}</p>
		<p>&copy; {{date("Y")}} {{domainName()}} - @LANG('ui.All Rights Reserved')</p>
		<span class="footer-links">
			<a href="#top">@LANG('ui.Back to Top')</a>&bull;
			<a href="/privacy">@LANG('ui.Privacy Policy')</a>&bull;
			<a href="/terms">@LANG('ui.Terms of Use')</a>&bull;
			<a href="/contact">@LANG('ui.Contact')</a>&bull;
			<a href="/about">@LANG('ui.About')</a>
		</span>
	</div>
</footer>
