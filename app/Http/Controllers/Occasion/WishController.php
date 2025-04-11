<?php

namespace App\Http\Controllers\Occasion;

use App\Data\ImageData;
use App\Data\CreateOccasionData;
use App\Data\WishData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Occasion;
use App\Models\Wish;
use App\Traits\ImageTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class WishController extends Controller
{
    public function create(WishData $wishData): RedirectResponse
    {
        $occasion_id = $wishData->occasion_id;
        $occasion = Occasion::where('id', $occasion_id)->first();
        if (!$occasion)
            return response()->json([
                'message' => trans('occasions.not_found_occasion'),
            ], 404);

        $url = null;
        if (isset($wishData->toArray()['image'])) {
            $image = $wishData->toArray()['image'];
            $stored_image = $image->store('/public/wishes/' . $occasion_id);
            $url = Storage::url($stored_image);
            $url = env('APP_URL') . $url;
        }

        Wish::create([...$wishData->toArray(), "image" => $url]);

        return back()->with([
            'customer' => $occasion->customer,
            'occasion' => CreateOccasionData::from($occasion),
            'wishes' => $occasion->wishes()->orderby('created_at', 'desc')->paginate(),
        ]);
    }

    public function update(Wish $wish, WishData $wishData): RedirectResponse
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
        $wish->update([...$wishData->toArray(), 'image' => $wish->image]);

        $url = null;
        if (isset($wishData->toArray()['image'])) {
            $wishImage = str_replace(env('APP_URL') . '/storage', '', $wish->image);
            if (Storage::exists('/public' . $wishImage) && $wishImage != '/wish.png') {
                Storage::delete('/public' . $wishImage);
            }
            $image = $wishData->image;
            $stored_image = $image->store('/public/wishes/' . $occasion_id);
            $url = Storage::url($stored_image);
            $url = env('APP_URL') . $url;
            $wish->update(['image' => $url]);
        }

        return back()->with([
            'customer' => $occasion->customer,
            'occasion' => CreateOccasionData::from($occasion),
            'wishes' => $occasion->wishes()->orderby('created_at', 'desc')->paginate(),
        ]);
    }
}
