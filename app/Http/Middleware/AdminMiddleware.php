<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\RolePermission;
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

       $user = Auth::user();
        $role = $user->role;
        $permissions = $role->permissions->pluck('route')->toArray();
        $currentUri = Route::current()->uri();

        if (in_array($currentUri, $permissions)) {
            return $next($request);
        }

        // Redirect if user has no permission
        return redirect()->back()->with('failed', 'You do not have permission to access this route.');
    }
}
