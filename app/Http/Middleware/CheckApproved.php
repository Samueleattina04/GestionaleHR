<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->status === 'pending') {
            auth()->logout();
            return redirect()->route('login')
                ->with('warning', 'Il tuo account è in attesa di approvazione. Riceverai una notifica quando sarà approvato.');
        }

        if (auth()->check() && auth()->user()->status === 'suspended') {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Il tuo account è stato sospeso. Contatta l\'amministratore.');
        }

        return $next($request);
    }
}
