<?php

namespace App\Http\Middleware;

use App\Models\MobileApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MobileApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json([
                'success' => false,
                'message' => 'Bearer token wajib dikirim.',
            ], 401);
        }

        $token = MobileApiToken::with('user.company', 'user.role', 'user.store')
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if (! $token || $token->isExpired() || ! $token->user) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah expired.',
            ], 401);
        }

        $user = $token->user;
        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        $token->forceFill(['last_used_at' => now()])->save();

        return $next($request);
    }
}
