<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCertificateApproval
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Unauthorized access to certificate approval system.');
        }

        return $next($request);
    }
}
