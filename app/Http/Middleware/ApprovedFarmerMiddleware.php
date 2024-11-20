<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApprovedFarmerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user->farmer || !$user->farmer->IsApproved) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only approved farmers can access this resource.',
            ], 403);
        }

        return $next($request);
    }
}