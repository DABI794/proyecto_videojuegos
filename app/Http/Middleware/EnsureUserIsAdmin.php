<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege todas las rutas del panel de administración.
 *
 * Uso en routes/web.php:
 *
 *   Route::prefix('admin')
 *       ->middleware(['auth', 'admin'])
 *       ->group(function () {
 *           // rutas admin...
 *       });
 *
 * Registro en bootstrap/app.php (Laravel 11):
 *
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias(['admin' => \App\Http\Middleware\EnsureUserIsAdmin::class]);
 *   })
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si no está autenticado, redirige al login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Si está autenticado pero no es admin, devuelve 403
        if (! $request->user()->isAdmin()) {
            abort(403, 'Acceso denegado. Se requieren permisos de administrador.');
        }

        return $next($request);
    }
}
