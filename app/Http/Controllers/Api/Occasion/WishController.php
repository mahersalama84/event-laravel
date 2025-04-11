<?php

namespace App\Http\Controllers\Api\Occasion;

use App\Data\ImageData;
use App\Data\WishData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Occasion;
use App\Models\Wish;
use App\Traits\ImageTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class WishController extends Controller
{
    use ImageTrait;

    public function book(Request $request)
    {
        $request->validate([
            'wish_id' => 'required|uuid',
        ]);
        $wish = Wish::where('id', $request->wish_id)->first();
        if (!$wish)
            return response()->json([
                'message' => trans('wishes.not_found_wish'),
            ], 404);
        $dateNow = Carbon::now()->format('Y-m-d');
        $dateTwo = $wish->occasion->start_date->format('Y-m-d');
        if ($dateNow > $dateTwo)
            return response()->json([
                'message' => trans('occasions.ended_occasion_date'),
            ], 404);

        $owner = auth()->user();
        $customer = Customer::where('id', $owner->id)->first();
        if ($customer->id === $wish->occasion->customer->id)
            return response()->json([
                'message' => trans('wishes.cant_booked_for_owner'),
            ], 422);
        $message = null;
        $booked = null;
        if ($customer->bookedWishes->contains($request->wish_id)) {
            $customer->bookedWishes()->detach($request->wish_id);
            $message = trans("wishes.wish_unbooked");
            $booked = false;
        } else {
            $customer
                ->bookedWishes()
                ->attach($request->wish_id, [
                    'note' => $request->note ?? null,
                    'show' => $request->show ?? true,
                ]);
            $message = trans("wishes.wish_booked");
            $booked = true;
        }
        $occasion = $wish->occasion;
        $wish = Wish::where('id', $request->wish_id)
            ->with('customers.visibleWishes', function ($query) use ($occasion) {
                $query->where('occasion_id', '=', $occasion->id);
            })
            ->with('customers.hiddenWishes', function ($query) use ($occasion) {
                $query->where('occasion_id', '=', $occasion->id);
            })
            ->first();
        $other_customer = $occasion->customer;
        activity('Wish')
            ->causedBy($customer)
            ->performedOn($other_customer)
            ->event($booked ? 'Booked' : 'Unbooked')
            ->log($booked ? 'The customer book other customer wish.' : 'The customer unbooked other customer wish.');

        return response()->json([
            'booked' => $booked,
            'wish' => $wish,
            'message' => $message
        ], 200);
    }

    public function uploadImage(ImageData $imageData, Request $request)
    {
        $wish = Wish::find($request->id);
        if (!$wish)
            return response()->json([
                'message' => trans('wishes.not_found_wish'),
            ], 404);
        if (!auth()->user()->wishes->contains($wish))
            return response()->json([
                'message' => trans('wishes.cant_deleted_for_not_owner'),
            ], 404);
        $url = $this->uploadFile($imageData, $wish, 'wishes/' . $wish->occasion->id);
        $wish->update(['image' => $url]);
        return response()->json([
            'wish' => $wish,
            'message' => trans("wishes.image_uploaded")
        ], 200);
    }

    public function deleteImage(Request $request)
    {
        $wish = Wish::find($request->id);
        if (!$wish)
            return response()->json([
                'message' => trans('wishes.not_found_wish'),
            ], 404);
        if (!auth()->user()->wishes->contains($wish))
            return response()->json([
                'message' => trans('wishes.cant_deleted_for_not_owner'),
            ], 404);
        $this->deleteFile($wish, 'wishes');
        $wish->update(['image' => null]);
        return response()->json([
            'message' => trans("wishes.image_deleted")
        ], 200);
    }

    public function create(WishData $wishData)
    {
        $occasion_id = $wishData->occasion_id;
        $occasion = Occasion::where('id', $occasion_id)->first();
        if (!$occasion)
            return response()->json([
                'message' => trans('occasions.not_found_occasion'),
            ], 404);

        $dateNow = Carbon::now()->format('Y-m-d');
        $timeNow = Carbon::now()->timezone('Asia/Damascus')->format('H:i');
        $dateTwo = $occasion->start_date->format('Y-m-d');
        $timeTwo = $occasion->start_date->format('H:i');
        if ($dateNow > $dateTwo)
            return response()->json([
                'message' => trans('occasions.ended_occasion_date'),
            ], 404);
        else if ($dateNow == $dateTwo && $timeNow > $timeTwo)
            return response()->json([
                'message' => trans('occasions.ended_occasion_time'),
            ], 404);

        $url = null;
        if (isset($wishData->toArray()['image'])) {
            $image = $wishData->toArray()['image'];
            $stored_image = $image->store('/public/wishes/' . $occasion_id);
            $url = Storage::url($stored_image);
            $url = env('APP_URL') . $url;
        }

        $wish = Wish::create([...$wishData->toArray(), "image" => $url]);
        $_occasion = Occasion::where('id', $occasion->id)->withCount('wishes')->first();
        $_occasion->unsetRelation('attendence');

        $_wish = Wish::where('id', $wish->id)->with(['customers'])->first();
        return response()->json([
            'wish' => $_wish,
            'occasion' => $_occasion,
            'message' => trans('wishes.wish_created'),
        ], 200);
    }

    public function update(Wish $wish, WishData $wishData)
    {
        $occasion_id = $wishData->occasion_id;
        $occasion = Occasion::where('id', $occasion_id)->first();
        if (!$occasion)
            return response()->json([
                'message' => trans('occasions.not_found_occasion'),
            ], 404);
        if (!$wish)
            return response()->json([
                'message' => trans('wishes.not_found_wish'),
            ], 404);
        if (!auth()->user()->wishes->contains($wish))
            return response()->json([
                'message' => trans('wishes.cant_deleted_for_not_owner'),
            ], 404);
        $dateNow = Carbon::now()->format('Y-m-d');
        $timeNow = Carbon::now()->timezone('Asia/Damascus')->format('H:i');
        $dateTwo = $occasion->start_date->format('Y-m-d');
        $timeTwo = $occasion->start_date->format('H:i');
        if ($dateNow > $dateTwo)
            return response()->json([
                'message' => trans('occasions.ended_occasion_date'),
            ], 404);
        else if ($dateNow == $dateTwo && $timeNow > $timeTwo)
            return response()->json([
                'message' => trans('occasions.ended_occasion_time'),
            ], 404);
        $wish->update([...$wishData->toArray(), 'image' => $wish->image]);

        $url = null;
        if (isset($wishData->toArray()['image'])) {
            $wishImage = str_replace(env('APP_URL') . '/storage', '', $wish->image);
            if (Storage::exists('/public' . $wishImage) && $wishImage != '/wish.png') {
                Storage::delete('/public' . $wishImage);
            }
            $image = $wishData->image;
            $stored_image = $image->store('/public/wishes/' . $wish->occasion_id);
            $url = Storage::url($stored_image);
            $url = env('APP_URL') . $url;
            $wish->update(['image' => $url]);
        }

        $_wish = Wish::where('id', '=', $wish->id)
            ->with('customers', function ($customer) use ($occasion) {
                return $customer->whereDoesntHave('hiddenWishes')
                    ->with('visibleWishes', function ($wish) use ($occasion) {
                        $wish->where('occasion_id', '=', $occasion->id);
                    });
            })
            ->first();
        return response()->json([
            'wish' => $_wish,
            'message' => trans('wishes.wish_updated'),
        ], 200);
    }

    public function destroy(Wish $wish)
    {
        if (!$wish)
            return response()->json([
                'message' => trans('wishes.not_found_wish'),
            ], 404);
        if (!auth()->user()->wishes->contains($wish))
            return response()->json([
                'message' => trans('wishes.cant_deleted_for_not_owner'),
            ], 404);
        if (count($wish->customers) > 0)
            return response()->json([
                'message' => trans('wishes.booked_customers_exists'),
            ], 404);
        $wish->delete();
        $_occasion = Occasion::where('id', $wish->occasion->id)->withCount('wishes')->first();
        $_occasion->unsetRelation('attendence');
        return response()->json([
            'occasion' => $_occasion,
            'message' => trans('wishes.wish_deleted'),
        ], 200);
    }
}
