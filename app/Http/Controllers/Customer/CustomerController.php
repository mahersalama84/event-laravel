<?php

namespace App\Http\Controllers\Customer;

use App\Data\CreateCustomerData;
use App\Data\CustomerData;
use App\Data\UpdateCustomerData;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Occasion;
use App\Notifications\ActivateNotification;
use App\Stats\CustomerStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{

    public function index(Request $request): Response
    {
        return Inertia::render('Tabs/Customers/Index', [
            'filters' => $request->all('search', 'role', 'is_active', 'prefix'),
            'sorts' => ["sortBy" => $request->sortBy ?? "created_at", "sortType" => $request->sortType ?? "desc"],
            'paginate' => Customer::orderByAll($request->sortBy, $request->sortType)
                ->filter($request->only('search', 'role', 'is_active', 'prefix'))
                ->paginate($request->per_page ?? 10)
                ->withQueryString()
                ->through(fn($customer) => [
                    'id' => $customer->id,
                    'is_active' => $customer->is_active,
                    'image' => $customer->image,
                    'full_name' => $customer->full_name,
                    'mobile' => $customer->mobile,
                    'email' => $customer->email,
                    'prefix' => $customer->prefix,
                    'mobile_no' => $customer->mobile_no,
                    'occasions_count' => count($customer->occasions),
                    'role' => $customer->roles[0]->name,
                ]),
        ]);
    }


    public function create()
    {
        return Inertia::render('Tabs/Customers/Create');
    }

    public function store(CreateCustomerData $customerData)
    {
        $url = null;
        if ($customerData->image) {
            $image = $customerData->image;
            $stored_image = $image->store('/public/customers');
            $url = Storage::url($stored_image);
            $url = env('APP_URL') . $url;
        }
        $customer = Customer::create([...$customerData->toArray(), "image" => $url]);
        $customer->assignRole('customer');
        CustomerStats::increase(1, $customer->created_at);
        return to_route('customers.index')->with('success', 'customer_created');
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Tabs/Customers/Edit', [
            'customer' => CustomerData::from($customer),
            'occasions' => $customer
                ->occasions()
                ->orderby('created_at', 'desc')
                ->paginate(1)
                ->through(fn($occasion) => [
                    'id' => $occasion->id,
                    'title' => $occasion->title,
                    'description' => $occasion->description,
                    'start_date' => $occasion->start_date,
                    'start_time' => $occasion->start_time,
                    'full_name' => $occasion->customer->full_name,
                    'image' => $occasion->customer->image,
                    'wishes_count' => count($occasion->wishes)
                ]),
            'bookedWishes' => $customer
                ->bookedWishes()
                ->orderby('created_at', 'desc')
                ->paginate(1)
                ->through(fn($bookedwish) => [
                    'image' => $bookedwish->occasion->customer->image,
                    'full_name' => $bookedwish->occasion->customer->full_name,
                    'occasion_title' => $bookedwish->occasion->title,
                    'wish_title' => $bookedwish->title,
                    'note' => $bookedwish->pivot->note,
                    'show' => $bookedwish->pivot->show,
                ]),
        ]);
    }

    public function update(Customer $customer, UpdateCustomerData $customerData)
    {
        $customer->update([...$customerData->toArray(), "is_active" => $customer->is_active, "image" => $customer->image, "password" => $customer->password]);
        $url = null;
        if ($customerData->image) {
            $customerImage = str_replace(env('APP_URL') . '/storage', '', $customer->image);
            if (Storage::exists('/public' . $customerImage) && $customerImage != '/customer.png') {
                Storage::delete('/public' . $customerImage);
            }
            $image = $customerData->image;
            $stored_image = $image->store('/public/customers');
            $url = Storage::url($stored_image);
            $url = env('APP_URL') . $url;
            $customer->update(['image' => $url]);
        }
        if ($customerData->password) {
            $customer->update(['password' => $customerData->password]);
        }

        return to_route('customers.index')->with('success', 'customer_updated');
    }

    public function toggleActive(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);
        if ($customer->expoToken)
            $customer->notify(new ActivateNotification());
        return response()->json([
            'message' => trans('customers.active_updated'),
        ], 200);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return to_route('customers.index')->with('success', 'customer_deleted');
    }

    public function getOccasions(Customer $customer)
    {
        return response()->json(
            $customer->occasions()
                ->orderby('created_at', 'desc')
                ->paginate()
                ->through(fn($occasion) => [
                    'id' => $occasion->id,
                    'customer_id' => $occasion->customer_id,
                    'title' => $occasion->title,
                    'description' => $occasion->description,
                    'start_date' => $occasion->start_date,
                    'start_time' => $occasion->start_time,
                    'full_name' => $occasion->customer->full_name,
                    'image' => $occasion->customer->image,
                    'wishes_count' => count($occasion->wishes)
                ]),
        );
    }

    public function DeleteOccasion(Occasion $occasion)
    {
        $occasion->delete();
        return response()->json([
            'message' => trans('occasions.occasion_deleted'),
        ], 200);
    }
}
