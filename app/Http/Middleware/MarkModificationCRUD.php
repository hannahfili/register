<?php

namespace App\Http\Middleware;

use App\Helpers\Abilities;
use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;

class MarkModificationCRUD
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Helper::checkIfUserIsAuthorized($request, Abilities::MARK_MODIFICATION_CRUD)) {
            return response()->json(['status' => 401, 'data' => 'Użytkownik nie jest uprawniony do wybranego zasobu'], 401);
        }
        return $next($request);
    }
}
