<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está logado E se a role é 'admin'
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Caso contrário, redireciona ou retorna erro 403 (Proibido)
        return abort(403, 'Acesso não autorizado. Apenas Administradores podem acessar esta área.');
    }
}