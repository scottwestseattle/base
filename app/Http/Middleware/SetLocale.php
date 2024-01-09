<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use Cookie;

class SetLocale
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
        //dump($request->locale);

        $locale = Cookie::get('locale');
        if (!is_null($request->locale))
        {
            app()->setLocale($request->locale);
            if (strcmp($locale, $request->locale) === 0)
            {
                // current locale already set in cookie
            }
            else
            {
                // set locale in cookie
                Cookie::queue('locale', $request->locale, COOKIE_WEEK);
            }
        }
        else
        {
            // not set, set from cookie or default to English
            $locale = isset($locale) ? $locale : 'en';
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
