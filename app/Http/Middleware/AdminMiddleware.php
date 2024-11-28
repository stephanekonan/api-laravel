<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Veuillez vous connecter'
            ], 401);
        }

        if (Auth::check() && (Auth::user()->role !== 'admin')) {
            return response()->json([
                'error' => 'Accès non autorisé. Veuillez contacter l\'administration.'
            ], 401);
        }

        return $next($request);
    }
}
