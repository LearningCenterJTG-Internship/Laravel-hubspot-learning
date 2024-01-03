<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class HubspotAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = 1; # id to be finished

        if ($this->userExists($userId)) {
            return $next($request);
        }

        return response("Unauthorized user!", 401);
    }

    private function userExists($userId) {
        return User::where('id', $userId)->exists();
    }
}
