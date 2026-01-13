<?php

namespace App\Http\Middleware;

use App\Models\IpAccessControl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpAccessControlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // 1. Check Blacklist
        $isBlacklisted = IpAccessControl::where('ip_address', $ip)
            ->where('type', 'blacklist')
            ->where('is_active', true)
            ->exists();

        if ($isBlacklisted) {
            abort(403, 'Your IP address is blocked.');
        }

        // 2. Check Whitelist (If any whitelists exist, IP must be in one of them)
        $hasWhitelists = IpAccessControl::where('type', 'whitelist')
            ->where('is_active', true)
            ->exists();

        if ($hasWhitelists) {
            $isWhitelisted = IpAccessControl::where('ip_address', $ip)
                ->where('type', 'whitelist')
                ->where('is_active', true)
                ->exists();

            if (!$isWhitelisted) {
                abort(403, 'Access restricted to authorized networks only.');
            }
        }

        return $next($request);
    }
}
