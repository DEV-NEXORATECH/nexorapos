<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Redirect to login if user is not authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get the current authenticated user
        $user = Auth::user();

        // Collect all permission IDs assigned to the user's roles
        $userPermissionIds = $user->roles
            ->flatMap(fn($role) => $role->permissions)
            ->pluck('id')
            ->unique()
            ->toArray();

        // Fetch all system permissions and filter only those the user has
        $userRoutes = Permission::whereIn('id', $userPermissionIds)
            ->pluck('route')
            ->toArray();

        // Get the current route URI
       $currentUri = Route::getFacadeRoot()->current()->uri();

        // Check if the user has permission to access this route
        if (in_array($currentUri, $userRoutes)) {
            return $next($request);
        }

        // Redirect back if user has no permission
        return redirect()->back()->with('failed', 'You do not have permission to access this route.');
    }
}
