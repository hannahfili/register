<?php

namespace App\Http\Middleware;

use App\Helpers\Abilities;
use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;
use PHPUnit\TextUI\Help;

class SubjectREAD
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
        if (!Helper::checkIfUserIsAuthorized($request, Abilities::SUBJECT_READ)) {
            return response()->json(['status' => 401, 'data' => 'Użytkownik nie jest uprawniony do wybranego zasobu'], 401);
        }
        return $next($request); 
    }
}
