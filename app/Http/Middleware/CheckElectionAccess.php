<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Election;

class CheckElectionAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $election = $request->route('election');

        if (!$election) {
            return $next($request);
        }

        $user = auth()->user();

        // User can access if they created the election or are assigned as sub-admin
        if ($user->id === $election->created_by || $election->subAdmins()->where('user_id', $user->id)->exists()) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized access to this election'], 403);
    }
}
