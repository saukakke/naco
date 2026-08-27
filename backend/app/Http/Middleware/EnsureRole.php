<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request,Closure $next,string ...$roles):Response
    {
        $user=$request->user();
        abort_unless($user && collect($roles)->contains(fn(string $role)=>$user->hasRole($role) || ($role==='admin' && $user->hasGlobalAccess())),403,'Insufficient role privileges.');
        return $next($request);
    }
}
