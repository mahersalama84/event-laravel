<?php

namespace App\Http\Controllers\Api\Expo;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Notifications\ActivateNotification;
use YieldStudio\LaravelExpoNotifier\Models\ExpoNotification;
use YieldStudio\LaravelExpoNotifier\Models\ExpoToken;

class ExpoController extends Controller
{
    //
    public function notify()
    {
        $customer = Customer::where('mobile', '999999999')->first();
        $customer->notify(new ActivateNotification());
        return response()->json([
            'status' => true,
        ], 200);
    }
    public function notifications()
    {
        $notifications = ExpoNotification::all();
        return response()->json($notifications, 200);
    }
    public function MakeToken()
    {
        $customer = Customer::where('mobile', '999999999')->first();
        if ($customer->expoToken)
            $customer->expoToken->delete();
        ExpoToken::create([
            'owner_type' => get_class($customer),
            'owner_id' => $customer->id,
            'value' => 'ExponentPushToken[jdjyH3JOtOCXCJ6mBIr-8J]',
        ]);
        return response()->json([
            'status' => true,
        ], 200);
    }

    public function DeleteToken()
    {
        $customer = Customer::where('mobile', '999999999')->first();

        $token = ExpoToken::where('owner_type', get_class($customer))->where('owner_id', $customer->id);
        $token->delete();
        return response()->json([
            'status' => true,
        ], 200);
    }
}
