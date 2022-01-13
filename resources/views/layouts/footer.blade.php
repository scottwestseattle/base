<!-- FOOTER -->
<footer class="footer backin-black">
	<div class="container text-center p-5">
		<a class="" href="/">
			<div class="brand logo middle">
				<svg class="bi  app-color-primary-reverse" width="50" height="50" fill="currentColor" >
				    <use xlink:href="/img/bootstrap-icons.svg#{{getLogo()}}" />
			    </svg>
			</div>
		</a>

		<p style="font-size:2em;" class="footer-heading">{{domainName()}}</p>
    	<p style="font-size:1.2em;" class="">{{App\Site::getTitle()}}</p>
		<p>@LANG('base.All Rights Reserved') &copy; 2020 &ndash; {{date("Y")}}</p>
		<span class="footer-links">
			<a href="#top">@LANG('base.Back to Top')</a>&bull;
			<a href="{{lurl('privacy')}}">@LANG('base.Privacy Policy')</a>&bull;
			<a href="{{lurl('terms')}}">@LANG('base.Terms of Use')</a>&bull;
			<a href="{{lurl('sitemap')}}">@LANG('base.Site Map')</a>&bull;
			<a href="{{lurl('about')}}">@LANG('base.About')</a>
		</span>
	</div>
</footer>
