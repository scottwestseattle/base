<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\User;

class isOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		//
		// This only works when the full model is passed as a parameter
		//
		if (Auth::check())
		{
			// admin can change anything
			if (User::isAdmin())
				return $next($request);
			
			$p = $request->route()->parameters();
			if (isset($p))
			{
				foreach($p as $record)
				{
					// if it's the user model, then check 'id'
					if (is_a($record, 'App\User')) 
					{
						if ($record->id == Auth::id())
						{
							// id's match
							return $next($request);
						}
					}
					// other models check 'user_id'
					else if (isset($record->user_id) && $record->user_id == Auth::id())
					{
						// id's match
						return $next($request);
					}
				}
			}
			
			// user logged in but he's not the owner			
			abort(401);
		}

		return redirect('/login'); // not logged in
    }
}
