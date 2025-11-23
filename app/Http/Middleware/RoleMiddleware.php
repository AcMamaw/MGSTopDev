<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
   public function handle($request, Closure $next, ...$roles)
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $userRole = auth()->user()->employee->role->role_name;

    if (!in_array($userRole, $roles)) {
        abort(403, 'Unauthorized Access');
    }

    return $next($request);
}

}
