<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckAccess
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $user = Auth::user(); // Obtener el usuario autenticado

        // Si no hay usuario autenticado, redirigir a AccessDenied
        if (!$user) {
            return Inertia::render('PaginaError/AccessDenied');
        }

        // Obtener los permisos del usuario asociados a su rol
        $userPermissions = $user->role->permisos->pluck('nombre')->toArray();
        $userRoleName = $user->role->nombre; // Nombre del rol del usuario

        // Separar permisos y roles de los argumentos ($guards)
        $requiredPermissions = [];
        $requiredRoles = [];

        foreach ($guards as $guard) {
            // Si el guard parece un permiso (por ejemplo, "ver_tutoriales"), lo tratamos como tal
            if (str_contains($guard, '_')) {
                $requiredPermissions[] = $guard;
            } else {
                // De lo contrario, lo tratamos como un nombre de rol
                $requiredRoles[] = $guard;
            }
        }

        // Verificar roles (si se especificaron)
        if (!empty($requiredRoles) && !in_array($userRoleName, $requiredRoles)) {
            return Inertia::render('PaginaError/AccessDenied');
        }

        // Verificar permisos (si se especificaron)
        if (!empty($requiredPermissions)) {
            foreach ($requiredPermissions as $permission) {
                if (!in_array($permission, $userPermissions)) {
                    return Inertia::render('PaginaError/AccessDenied');
                }
            }
        }

        // Compartir permisos con el frontend
        Inertia::share([
            'auth' => [
                'user' => [
                    'permissions' => $userPermissions,
                    'role' => $userRoleName,
                ],
            ],
        ]);

        return $next($request);
    }
}