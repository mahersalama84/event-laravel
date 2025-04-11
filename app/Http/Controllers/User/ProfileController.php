<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', 'user_updated');
        // return response()->json([
        //         'message'=> trans('users.user_updated')
        //     ],200);        
    }

    public function setShowCustomers(Request $request): JsonResponse
    {
        $owner = $request->user();
        $owner->show_customers_stats = !$owner->show_customers_stats;
        $owner->save();
        $owner->refresh();
        if($owner->show_customers_stats)
            return response()->json([
                    'message'=> trans('customers.show_customers_updated')
                ],200);
        else
            return response()->json([
                    'message'=> trans('customers.not_show_customers_updated')
                ],200);
    }
    
    public function setShowUsers(Request $request): JsonResponse
    {
        $owner = $request->user();
        $owner->show_users_stats = !$owner->show_users_stats;
        $owner->save();
        $owner->refresh();
        if($owner->show_users_stats)
            return response()->json([
                    'message'=> trans('users.show_users_updated')
                ],200);
        else
            return response()->json([
                    'message'=> trans('users.not_show_users_updated')
                ],200);     
    }      

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
