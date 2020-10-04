<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use Cookie;

class locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {	
		//sbw COULDN'T USE THIS BECAUSE SESSION ISN'T AVAILABLE AT THIS POINT
	
		// if the URL starts with the locale like '/en/about'
		$locale = $request->segment(1);
		if (strlen($locale) == 2 && in_array($locale, ['en', 'es', 'zh']))
		{
			// set the locale and remove the prefix
			app()->setLocale($locale);
			session(['locale' => $locale]);
			
			$url = '';
			$skipped = false;
			$segments = $request->segments();
			foreach($segments as $segment)
			{
				if (!$skipped && $segment == $locale)
				{
					// skip the first locale
					$skipped = true;
				}
				else
				{
					$url .= '/' . $segment;
				}
			}

			return redirect($url);
		}
		
        return $next($request);
    }
}
