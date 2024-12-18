<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class CheckBlacklist
{
    public function handle($request, Closure $next)
    {
        if (DB::table('blacklist')->where('email', $request->email)->exists()) {
            return redirect()->back()->withErrors(['email' => 'This email is not allowed to register.']);
        }

        return $next($request);
    }
}
