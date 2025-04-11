<?php

namespace App\Http\Controllers\Occasion;

use App\Data\CreateOccasionData;
use App\Data\UpdateOccasionData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Occasion;
use App\Models\Wish;
use App\Traits\OccasionTrait;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class OccasionController extends Controller
{
    use OccasionTrait;
    public function index(Request $request): Response
    {
        return Inertia::render('Tabs/Occasions/Index', [
            'filters' => $request->all('search', 'start_date'),
            'sorts' => ["sortBy" => $request->sortBy ?? "created_at", "sortType" => $request->sortType ?? "desc"],
            'paginate' => Occasion::orderByAll($request->sortBy, $request->sortType)
                ->filter($request->only('search', 'start_date'))
                ->paginate($request->per_page ?? 10)
                ->withQueryString()
                ->through(fn($occasion) => [
                    'id' => $occasion->id,
                    'title' => $occasion->title,
                    'description' => $occasion->description,
                    'start_date' => $occasion->start_date,
                    'start_time' => $occasion->start_time,
                    'full_name' => $occasion->customer->full_name,
                    'image' => $occasion->customer->image,
                    'customer' => $occasion->customer,
                    'wishes_count' => count($occasion->wishes)
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Tabs/Occasions/Create');
    }

    public function searchCustomer(Request $request)
    {
        return response()->json([
            'customers' => Customer::filter($request->only('search'))->select(['id', 'first_name', 'last_name', 'mobile'])->get(),
        ]);
    }

    public function store(CreateOccasionData $occasionData)
    {
        $dt = $this->toDateTime($occasionData);
        Occasion::create([...$occasionData->toArray(), 'start_date' => $dt]);
        return to_route('occasions.index')->with('success', 'occasion_created');
    }

    public function edit(Occasion $occasion)
    {
        return Inertia::render('Tabs/Occasions/Edit', [
            'customer' => $occasion->customer,
            'occasion' => UpdateOccasionData::from($occasion),
            'wishes' => $occasion->wishes()->orderby('created_at', 'desc')->paginate(1),
        ]);
    }

    public function update(Occasion $occasion, CreateOccasionData $occasionData)
    {
        $dt = $this->toDateTime($occasionData);
        $occasion->update([...$occasionData->toArray(), 'start_date' => $dt]);
        return to_route('occasions.index')->with('success', 'occasion_updated');
    }

    public function destroy(Occasion $occasion)
    {
        $occasion->delete();
        return to_route('occasions.index')->with('success', 'occasion_deleted');
    }

    public function getWishes(Occasion $occasion)
    {
        return response()->json(
            $occasion->wishes()->with('customers')->orderby('created_at', 'desc')->paginate(),
        );
    }

    public function DeleteWish(Wish $wish)
    {
        $wish->delete();
        return response()->json([
            'message' => trans('wishes.wish_deleted'),
        ], 200);
    }
}
