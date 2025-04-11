<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\Occasion;
use App\Models\Wish;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAcceptedFollowing
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->following_id || $request->occasion_id || $request->wish_id) {
            $owner = auth()->user();
            $customer = Customer::where('id', $owner->id)->first();
            if ($request->following_id && $request->following_id == $owner->id)
                $is_accepted = true;
            else if ($request->following_id)
                $is_accepted = $customer->acceptedFollowings->contains($request->following_id);
            else if ($request->occasion_id) {
                $occasion = Occasion::where('id', $request->occasion_id)->first();
                if ($owner->id == $occasion->customer_id) $is_accepted = true;
                else $is_accepted = $customer->acceptedFollowings->contains($occasion->customer_id);
            } else if ($request->wish_id) {
                $wish = Wish::where('id', $request->wish_id)->first();
                if ($owner->id == $wish->occasion->customer_id) $is_accepted = true;
                else $is_accepted = $customer->acceptedFollowings->contains($wish->occasion->customer_id);
            }
            if (!$is_accepted)
                return response()->json([
                    'status' => false,
                    'message' => trans('customers.followingnotaccepted')
                ], 404);
        }
        return $next($request);
    }
}
