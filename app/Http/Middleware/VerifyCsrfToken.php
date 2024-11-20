<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class VerifyCsrfToken
{
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('post') && $request->session()->token() !== $request->input('_token')) {
            return redirect()->back()->withErrors(['CSRF token mismatch.']);
        }

        return $next($request);
    }
}
