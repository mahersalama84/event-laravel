<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsFollower
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->follower_id) {
            $owner = auth()->user();
            $customer = Customer::where('id', $owner->id)->first();
            $is_follower = $customer->followers->contains($request->follower_id);
            if (!$is_follower)
                return response()->json([
                    'status' => false,
                    'message' => trans('customers.followernotfound')
                ], 404);
        }
        return $next($request);
    }
}
