<?php

namespace App\Http\Controllers\Api\Customer;

use App\Data\ImageData;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Notifications\OtpNotification;
use App\Stats\CustomerStats;
use App\Traits\ImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use YieldStudio\LaravelExpoNotifier\Models\ExpoToken;

class CustomerController extends Controller
{
    use ImageTrait;

    public function RegisterExpoPushToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);
        $owner = auth()->user();
        $customer = Customer::where('id', $owner->id)->first();
        if ($customer->expoToken)
            $customer->expoToken->delete();
        ExpoToken::create([
            'owner_type' => get_class($customer),
            'owner_id' => $customer->id,
            'value' => $request->token,
        ]);
        return response()->json([
            'status' => true,
        ], 200);
    }

    public function processFollower(Request $request)
    {
        $owner = auth()->user();
        $request->validate([
            'follower_id' => 'required|uuid|not_in:' . $owner->id,
        ]);
        $customer = Customer::where('id', $owner->id)->first();
        $follower = Customer::where('id', $request->follower_id)->first();
        if (!$follower)
            return response()->json([
                'status' => false,
                'message' => trans('customers.customernotfound')
            ], 404);
        $is_accepted = $customer->acceptedFollowers->contains($request->follower_id);
        if ($is_accepted)
            $customer->followers()->updateExistingPivot($request->follower_id, ['accepted' => false]);
        else $customer->followers()->updateExistingPivot($request->follower_id, ['accepted' => true]);
        activity('Customer')
            ->causedBy($customer)
            ->performedOn($follower)
            ->event(!$is_accepted ? 'Accepted' : 'Unaccepted')
            ->log(!$is_accepted ? 'The customer accepted other customer.' : 'The customer unaccepted other customer.');
        return response()->json([
            'status' => true,
            'accepted' => !$is_accepted,
            'message' =>  $is_accepted ? trans('customers.unaccept_follower') : trans('customers.accept_follower')
        ], 200);
    }

    public function follow(Request $request)
    {
        $owner = auth()->user();
        $request->validate([
            'customer_id' => 'required|uuid|not_in:' . $owner->id,
        ]);
        $customer = Customer::where('id', $owner->id)->first();
        $other_customer = Customer::where('id', $request->customer_id)->first();
        if (!$other_customer)
            return response()->json([
                'status' => false,
                'message' => trans('customers.customernotfound')
            ], 404);
        $followings = $customer->followings->contains($request->customer_id);
        if ($followings)
            $customer->followings()->detach($request->customer_id);
        else
            $customer->followings()->attach($request->customer_id);

        $customer->unsetRelation('followings');
        activity('Customer')
            ->causedBy($customer)
            ->performedOn($other_customer)
            ->event(!$followings ? 'Followed' : 'Unfollowed')
            ->log(!$followings ? 'The customer followed other customer.' : 'The customer unfollowed other customer.');
        return response()->json([
            'status' => true,
            'follow' => !$followings,
            'message' => !$followings ?  trans('customers.followed') :  trans('customers.un_followed')
        ], 200);
    }

    public function paginateFollowers(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|uuid',
        ]);
        $per_page = $request->per_page ?? 10;
        $customer = Customer::where('id', $request->customer_id)->first();
        if (!$customer)
            return response()->json([
                'status' => false,
                'message' => trans('customers.customernotfound')
            ], 404);
        $search = $request->search ?? null;
        $followers = [];
        if ($search)
            $followers = $customer->followers()->searchCustomer($request->only('search'))->paginate($per_page, ['id', 'first_name', 'last_name', 'prefix', 'mobile', 'image']);
        else
            $followers = $customer->followers()->paginate($per_page, ['id', 'first_name', 'last_name', 'prefix', 'mobile', 'image']);
        return  response($followers->toArray()['data'], 200);
    }

    public function paginateFollowings(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|uuid',
        ]);
        $per_page = $request->per_page ?? 10;
        $customer = Customer::where('id', $request->customer_id)->first();
        if (!$customer)
            return response()->json([
                'status' => false,
                'message' => trans('customers.customernotfound')
            ], 404);
        $search = $request->search ?? null;
        $following = [];
        if ($search)
            $following = $customer->followings()->searchCustomer($request->only('search'))->paginate($per_page, ['id', 'first_name', 'last_name', 'prefix', 'mobile', 'image']);
        else
            $following = $customer->followings()->paginate($per_page, ['id', 'first_name', 'last_name', 'prefix', 'mobile', 'image']);
        return  response($following->toArray()['data'], 200);
    }

    public function search(Request $request): JsonResponse
    {
        $owner = auth()->user();
        $request->validate([
            'search' => 'required|string|max:30',
        ]);
        $customers = Customer::orderByAll("created_at", "desc")
            // ->where('id', '!=', $owner->id)
            ->searchCustomer($request->only('search'))
            ->paginate($request->per_page ?? 10);
        return response()->json($customers->toArray()['data'], 200);
    }

    public function paginateCustomers(Request $request): JsonResponse
    {
        $customers = Customer::orderByAll("created_at", "desc")
            ->paginate($request->per_page ?? 10);
        return response()->json($customers->toArray()['data'], 200);
    }
    //Get profile occasions
    public function myOccasions(Request $request): JsonResponse
    {
        $owner = auth()->user();
        $customer = Customer::where('id', $owner->id)->first();
        $occasions = $customer->occasions()
            ->orderby('created_at', 'desc')
            ->paginate($request->per_page ?? 10)
            ->through(fn($occasion) => [
                'id' => $occasion->id,
                'customer' => $occasion->customer,
                'customer_id' => $occasion->customer_id,
                'title' => $occasion->title,
                'description' => $occasion->description,
                'start_date' => $occasion->start_date,
                'start_time' => $occasion->start_time,
                'wishes_count' => count($occasion->wishes),
                'attendence_count' => $occasion->attendence()->count(),
                'attendence_ids' => $occasion->attendence()->pluck('customer_id'),
            ]);
        return response()->json($occasions->toArray()['data'], 200);
    }

    //get Occasions for any customer
    public function otherOccasions(Request $request): JsonResponse
    {
        $request->validate([
            'following_id' => 'required|uuid',
        ]);
        $customer = Customer::where('id', $request->following_id)->first();
        $occasions = $customer->occasions()
            ->orderby('created_at', 'desc')
            ->paginate($request->per_page ??  10)
            ->through(fn($occasion) => [
                'id' => $occasion->id,
                'customer' => $occasion->customer,
                'customer_id' => $occasion->customer_id,
                'title' => $occasion->title,
                'description' => $occasion->description,
                'start_date' => $occasion->start_date,
                'start_time' => $occasion->start_time,
                'wishes_count' => count($occasion->wishes),
                'attendence_count' => $occasion->attendence()->count(),
                'attendence_ids' => $occasion->attendence()->pluck('customer_id'),
            ]);
        return response()->json($occasions->toArray()['data'], 200);
    }

    //get Occasions for followings
    public function FollowingsOccasions(Request $request): JsonResponse
    {
        $owner = auth()->user();
        $customer = Customer::where('id', $owner->id)->first();
        $search = $request->search ?? null;
        if ($search)
            $occasions = $customer->followingsoccasions()
                ->searchOccasion($request->only('search'))
                ->orderby('created_at', 'desc')
                ->paginate($request->per_page ??  10)
                ->through(fn($occasion) => [
                    'id' => $occasion->id,
                    'customer' => $occasion->customer,
                    'customer_id' => $occasion->customer_id,
                    'title' => $occasion->title,
                    'description' => $occasion->description,
                    'start_date' => $occasion->start_date,
                    'start_time' => $occasion->start_time,
                    'wishes_count' => count($occasion->wishes),
                    'attendence_count' => $occasion->attendence()->count(),
                    'attendence_ids' => $occasion->attendence()->pluck('customer_id'),
                ]);
        else
            $occasions = $customer->followingsoccasions()
                ->orderby('created_at', 'desc')
                ->paginate($request->per_page ??  10)
                ->through(fn($occasion) => [
                    'id' => $occasion->id,
                    'customer' => $occasion->customer,
                    'customer_id' => $occasion->customer_id,
                    'title' => $occasion->title,
                    'description' => $occasion->description,
                    'start_date' => $occasion->start_date,
                    'start_time' => $occasion->start_time,
                    'wishes_count' => count($occasion->wishes),
                    'attendence_count' => $occasion->attendence()->count(),
                    'attendence_ids' => $occasion->attendence()->pluck('customer_id'),
                ]);
        return response()->json($occasions->toArray()['data'], 200);
    }

    public function uploadImage(ImageData $imageData)
    {
        $id = auth()->user()->id;
        $customer = Customer::find($id);
        $url = $this->uploadFile($imageData, $customer, 'customers/' . $id);
        $customer->update(['image' => $url]);
        return response()->json([
            'image' => $url,
            'message' => trans("customers.image_uploaded")
        ], 200);
    }

    public function deleteImage()
    {
        $id = auth()->user()->id;
        $customer = Customer::find($id);
        $this->deleteFile($customer, 'customers');
        $customer->update(['image' => null]);
        activity()->enableLogging();
        return response()->json([
            'message' => trans("customers.image_deleted")
        ], 200);
    }

    public function otpLogin(Request $request)
    {
        activity()->disableLogging();
        $validatedData = $request->validate([
            'prefix' => 'required|numeric|in:971,46',
            'mobile' => 'required|numeric|digits:9',
        ]);
        $otp = rand(1000, 9999);
        if ($validatedData['prefix'] == '971' && $validatedData['mobile'] == '956031740') $otp = 9999;
        $customer = Customer::where('prefix', $validatedData['prefix'])->where('mobile', $validatedData['mobile'])->first();
        $exist = $customer && $customer->first_name ? true : false;
        if (!$exist) {
            return response()->json([
                'status' => false,
                'message' => trans('customers.customernotfound')
            ], 404);
        } else {
            $customer->password = $otp;
            $customer->save();
        }

        activity()->enableLogging();
        activity('Customer')
            ->causedBy($customer)
            ->performedOn($customer)
            ->event('Request otpLogin')
            ->log('The customer has requested otp to login.');

        $customer->notify(new OtpNotification($otp));
        return response()->json([
            'exist' => $exist,
            'otp' => $otp,
        ], 200);
    }

    public function otpGuest(Request $request)
    {
        activity()->disableLogging();
        $validatedData = $request->validate([
            'prefix' => 'required|numeric|in:971,46',
            'mobile' => 'required|numeric|digits:9',
            'email' => 'required|string|email|max:100|unique:customers,email',
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30'
        ]);
        $otp = rand(1000, 9999);
        $customer = Customer::where('prefix', $validatedData['prefix'])->where('mobile', $validatedData['mobile'])->first();
        $exist = false;
        $message = null;
        if ($customer && $customer->first_name && $customer->mobile_verified_at) {
            $exist = true;
            $message = 'customeralreadyverified';
            $customer->password = $otp;
            $customer->save();
        } else if ($customer && $customer->first_name && !$customer->mobile_verified_at) {
            $exist = true;
            $message = 'customermustverify';
            // $customer->name = $request->name;
            $customer->password = $otp;
            $customer->save();
        } else if ($customer && !$customer->first_name) {
            $exist = false;
            $message = 'customermustentername';
            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->email = $request->email;
            $customer->password = $otp;
            $customer->save();
        } else {
            $exist = false;
            $message = 'customerregistered';
            $customer = Customer::create([
                'prefix' => $validatedData['prefix'],
                'mobile' => $validatedData['mobile'],
                'email' => $validatedData['email'],
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'password' => $otp,
                'mobile_verified_at' => null,
            ]);
            $customer->assignRole('customer');
            CustomerStats::increase(1, $customer->created_at);
        }
        activity()->enableLogging();
        activity('Customer')
            ->causedBy($customer)
            ->performedOn($customer)
            ->event('Request otpGuest')
            ->log('The customer has requested otp to sign up.');

        $customer->notify(new OtpNotification($otp));
        return response()->json([
            'exist' => $exist,
            'otp' => $otp,
            'message' => trans("customers.$message")
        ], 200);
    }

    public function login(Request $request)
    {
        activity()->disableLogging();
        $request->validate([
            'prefix' => 'required|numeric|in:971,46',
            'mobile' => 'required|numeric|digits:9',
            'password' => 'required|numeric|digits:4',
        ]);
        $customer = Customer::where('prefix', $request['prefix'])->where('mobile', $request['mobile'])->first();
        if (!$customer)
            return response()->json(['message' => trans('customers.customernotfound')], 404);
        if (!$customer->password)
            return response()->json(['message' => trans('customers.mustgetotp')], 422);

        if (!Auth::guard('customers')->attempt($request->only('prefix', 'mobile', 'password'))) {
            return response()->json([
                'message' => trans('customers.invalidlogindetails')
            ], 401);
        }
        $customer->mobile_verified_at = now();
        $customer->password = null;
        $customer->save();
        $token = $customer->createToken('auth_token')->plainTextToken;
        $_customer = Customer::find($customer->id);
        $_customer->unsetRelation('attendence');

        activity()->enableLogging();
        activity('Customer')
            ->causedBy($_customer)
            ->performedOn($_customer)
            ->event('Logged in')
            ->log('The customer has logged in.');

        return response()->json([
            'customer' => $_customer,
            'token' => $token,
            'message' => trans('customers.customerloggedin'),
            // 'id' => $customer->id,
            // 'token_type' => 'Bearer',
        ], 200);
    }

    public function profile()
    {
        if (!auth()->user()->mobile_verified_at)
            return response()->json(['message' => trans('customers.notverifiedcustomer')], 422);
        $id = auth()->user()->id;
        $customer = Customer::find($id);
        $customer->unsetRelation('attendence');
        return response()->json($customer, 200);
    }

    public function updateProfile(Request $request)
    {
        $owner = auth()->user();
        $customer = Customer::find($owner->id);
        if (!$customer)
            return response()->json(['message' => trans('customers.customernotfound')], 404);
        if (!$customer->mobile_verified_at)
            return response()->json(['message' => trans('customers.notverifiedcustomer')], 422);
        $request->validate([
            'email' => 'required|string|email|max:100|unique:customers,email,' . $customer->id . ',id',
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
        ]);
        $customer->update([
            'email' => $request['email'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
        ]);
        $customer = Customer::find($customer->id);
        return response()->json([
            'customer' => $customer,
            'message' => trans('customers.customerupdated'),
        ], 200);
    }

    public function logout()
    {
        $owner = auth()->user();
        activity()->disableLogging();
        if (!$owner->mobile_verified_at)
            return response()->json(['message' => trans('customers.notverifiedcustomer')], 422);
        $customer = Customer::where('id', $owner->id)->first();

        $expoPushToken = ExpoToken::where('owner_type', get_class($customer))->where('owner_id', $customer->id);
        $expoPushToken->delete();

        $customer->tokens()->delete();
        $customer->mobile_verified_at = null;
        $customer->password = null;
        $customer->save();
        activity()->enableLogging();
        activity('Customer')
            ->causedBy($customer)
            ->performedOn($customer)
            ->event('Logged out')
            ->log('The customer has logged out.');
        return response()->json(['message' => trans('customers.customersignedout')], 200);
    }
}
