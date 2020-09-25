<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class isSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
		if (User::isSuperAdmin())
			return $next($request);

        return redirect('/login');
    }
}
