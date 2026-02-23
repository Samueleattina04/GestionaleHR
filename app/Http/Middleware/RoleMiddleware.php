<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Il tuo account non è ancora stato approvato o è stato sospeso.');
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Non hai i permessi per accedere a questa pagina.');
        }

        return $next($request);
    }
}
