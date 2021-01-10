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
		<p>&copy; {{date("Y")}} {{domainName()}} - @LANG('base.All Rights Reserved')</p>
		<span class="footer-links">
			<a href="#top">@LANG('base.Back to Top')</a>&bull;
			<a href="{{lurl('privacy')}}">@LANG('base.Privacy Policy')</a>&bull;
			<a href="{{lurl('terms')}}">@LANG('base.Terms of Use')</a>&bull;
			<a href="{{lurl('sitemap')}}">@LANG('base.Site Map')</a>&bull;
			<a href="{{lurl('about')}}">@LANG('base.About')</a>
		</span>
	</div>
</footer>
