<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckIfBlocked
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->blocked) {
            return redirect()->route('home')->with('error', 'Your account cannot post!');
        }

        return $next($request);
    }
}