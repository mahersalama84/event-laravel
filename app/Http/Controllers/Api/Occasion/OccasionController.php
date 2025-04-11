<?php

namespace App\Http\Controllers\Api\Occasion;

use App\Data\CreateOccasionData;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Occasion;
use App\Traits\OccasionTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OccasionController extends Controller
{
    use OccasionTrait;
    public function paginateAttendence(Request $request)
    {
        $request->validate([
            'occasion_id' => 'required|uuid',
        ]);
        $occasion = Occasion::where('id', $request->occasion_id)->first();
        if (!$occasion)
            return response()->json([
                'message' => trans('occasions.not_found_occasion'),
            ], 404);
        $attendence = $occasion
            ->attendence()
            ->with('visibleWishes', function ($query) use ($occasion) {
                $query->where('occasion_id', '=', $occasion->id);
            })
            ->with('hiddenWishes', function ($query) use ($occasion) {
                $query->where('occasion_id', '=', $occasion->id);
            })
            ->orderby('created_at', 'desc')
            ->paginate($request->per_page ?? 10)
            ->getCollection();
        return response()->json($attendence, 200);
    }

    public function attend(Request $request): JsonResponse
    {
        $request->validate([
            'occasion_id' => 'required|uuid',
        ]);
        $time_zone = $request->time_zone ?? "UTC";
        $occasion = Occasion::where('id', $request->occasion_id)->first();
        if (!$occasion)
            return response()->json([
                'message' => trans('occasions.not_found_occasion'),
            ], 404);
        $is_expired = $this->isExpired($occasion, $time_zone);
        if ($is_expired == 'date_expired')
            return response()->json([
                'message' => trans('occasions.ended_occasion_date'),
            ], 404);
        else if ($is_expired == 'time_expired')
            return response()->json([
                'message' => trans('occasions.ended_occasion_time'),
            ], 404);
        $owner = auth()->user();
        $customer = Customer::where('id', $owner->id)->first();
        $other_customer = $occasion->customer;
        if ($customer->id === $occasion->customer_id)
            return response()->json([
                'message' => trans('occasions.cant_attend_for_owner'),
            ], 422);

        $message = null;
        $attend = null;
        if ($customer->attendence->contains($request->occasion_id)) {
            $customer->attendence()->detach($request->occasion_id);
            $message = trans("occasions.not_attend");
            $attend = false;
        } else {
            $customer->attendence()->attach($request->occasion_id);
            $message = trans("occasions.attend");
            $attend = true;
        }
        activity('Occasion')
            ->causedBy($customer)
            ->performedOn($other_customer)
            ->event($attend ? 'Attend' : 'Not Attend')
            ->log($attend ? 'The customer will attend other customer occasion.' : 'The customer will not attend other customer occasion.');
        return response()->json([
            'attend' => $attend,
            'message' => $message
        ], 200);
    }

    public function paginateOccasions(Request $request): JsonResponse
    {
        $occasions = Occasion::orderby('created_at', 'desc')
            ->where("customer_id", "!=", auth()->user()->id)
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

    public function getWishes(Request $request)
    {
        $owner = auth()->user();
        $request->validate([
            'occasion_id' => 'required|uuid',
        ]);
        $occasion = Occasion::where('id', $request->occasion_id)->first();
        if (!$occasion)
            return response()->json([
                'message' => trans('occasions.not_found_occasion'),
            ], 404);
        $wishes = [];
        if ($owner->id == $occasion->customer_id) {
            $wishes = $occasion->wishes()
                ->with('customers', function ($customer) use ($occasion) {
                    return $customer->whereDoesntHave('hiddenWishes')
                        ->with('visibleWishes', function ($wish) use ($occasion) {
                            $wish->where('occasion_id', '=', $occasion->id);
                        });
                })
                ->paginate($request->per_page ?? 10)
                ->getCollection();
        } else {
            $wishes = $occasion->wishes()
                ->with('customers.visibleWishes', function ($query) use ($occasion) {
                    return $query->where('occasion_id', '=', $occasion->id);
                })
                ->with('customers.hiddenWishes', function ($query) use ($occasion) {
                    $query->where('occasion_id', '=', $occasion->id);
                })
                ->paginate($request->per_page ?? 10)
                ->getCollection();
        }

        return response()->json($wishes, 200);
    }

    public function create(CreateOccasionData $occasionData)
    {
        $time_zone = $occasionData->toArray()['time_zone'] ?? 'UTC';
        $dt = $this->toDateTime($occasionData);
        $pushed_occasion = Occasion::create([...$occasionData->toArray(), "customer_id" => auth()->user()->id, 'start_date' => $dt]);
        $occasion = Occasion::where('id', $pushed_occasion->id)->with(['customer'])->withCount('wishes')->first();
        return response()->json([
            'occasion' => $occasion,
            'message' => trans('occasions.occasion_created'),
        ], 200);
    }

    public function update(Occasion $occasion, CreateOccasionData $occasionData)
    {
        $time_zone = $occasionData->toArray()['time_zone'] ?? 'UTC';
        if (!$occasion)
            return response()->json([
                'message' => trans('occasions.not_found_occasion'),
            ], 404);

        if (!auth()->user()->occasions->contains($occasion))
            return response()->json([
                'message' => trans('occasions.cant_updated_for_not_owner'),
            ], 404);

        $is_expired = $this->isExpired($occasion, $time_zone);
        if ($is_expired == 'date_expired')
            return response()->json([
                'message' => trans('occasions.ended_occasion_date'),
            ], 404);
        else if ($is_expired == 'time_expired')
            return response()->json([
                'message' => trans('occasions.ended_occasion_time'),
            ], 404);

        $dt = $this->toDateTime($occasionData);

        $occasion->update([...$occasionData->toArray(), "customer_id" => auth()->user()->id, 'start_date' => $dt]);
        $_occasion = Occasion::where('id', $occasion->id)->withCount('wishes')->first();
        $_occasion->unsetRelation('attendence');

        return response()->json([
            'occasion' => $_occasion,
            'message' => trans('occasions.occasion_updated'),
        ], 200);
    }

    public function destroy(Request $request, Occasion $occasion)
    {
        $time_zone = $request->time_zone ?? "UTC";
        if (!$occasion)
            return response()->json([
                'message' => trans('occasions.not_found_occasion'),
            ], 404);
        if (!auth()->user()->occasions->contains($occasion))
            return response()->json([
                'message' => trans('occasions.cant_deleted_for_not_owner'),
            ], 404);

        if ($occasion->wishes->count() > 0)
            return response()->json([
                'message' => trans('occasions.has_wishes'),
            ], 404);

        $is_expired = $this->isExpired($occasion, $time_zone);
        if ($is_expired == 'date_expired')
            return response()->json([
                'message' => trans('occasions.ended_occasion_date'),
            ], 404);
        else if ($is_expired == 'time_expired')
            return response()->json([
                'message' => trans('occasions.ended_occasion_time'),
            ], 404);

        $occasion->delete();
        return response()->json([
            'message' => trans('occasions.occasion_deleted'),
        ], 200);
    }
}
