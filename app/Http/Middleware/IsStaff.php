<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está logado E se ele é staff OU admin
        if (auth()->check() && auth()->user()->isStaff()) {
            return $next($request);
        }

        // Se não tiver permissão
        return redirect('/dashboard')->with('error', 'Acesso não autorizado.');
    }
}