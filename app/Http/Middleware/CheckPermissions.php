<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $routeName = request()->route()->getName();
        // return response()->json([$routeName]);
        // Extract controller and function from the route name
        [$controller, $function] = explode('.', $routeName);

        // Define the permission name based on controller and function
        $permissionName = "{$controller}.{$function}";


        /* $permissions = $user->getAllPermissions();
        $roles = $user->getRoleNames();
        return response()->json([$routeName, $user->hasPermissionTo($permissionName), $permissions, $roles]); */


        // Check if the user has the basic permission (e.g., posts:update)
        if ($user->can($permissionName)) {
            return $next($request);
        }

        // If the user is an admin or has a higher role, check for the ':others' permission
        $higherPermissionName = "{$permissionName}.others";
        if ($user->can($higherPermissionName)) {
            return $next($request);
        }

        // If the user does not have the required permission, deny access
        return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
    }
}
