<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized admin access.'], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        // Strict role check for Admin entity
        $user = auth()->user();
        $adminRoles = [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_ELECTION_OFFICER];

        if (!in_array($user->role, $adminRoles)) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('home')->withErrors(['email' => 'Unauthorized access. Access restricted to administrative accounts.']);
        }

        return $next($request);
    }
}
