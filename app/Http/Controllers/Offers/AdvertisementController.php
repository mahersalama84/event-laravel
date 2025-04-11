<?php

namespace App\Http\Controllers\Offers;

use App\Data\AdvertisementData;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdvertisementController extends Controller
{
    public function togglePublished(Advertisement $advertisement)
    {
        $advertisement->update(['published' => !$advertisement->published]);
        return response()->json([
            'message' => trans('advertisements.published_updated'),
        ], 200);
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Tabs/Advertisements/Index', [
            'sorts' => ["sortBy" => "created_at", "sortType" =>  "desc"],
            'paginate' => Advertisement::orderByDate()
                ->filter($request->only('published'))
                ->paginate($request->per_page ?? 10)
                ->withQueryString()
                ->through(fn($advertisement) => [
                    'id' => $advertisement->id,
                    'image' => $advertisement->image,
                    'published' => $advertisement->published,
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Tabs/Advertisements/Create');
    }

    public function store(AdvertisementData $advertisementData)
    {
        $url = null;
        if ($advertisementData->image) {
            $image = $advertisementData->image;
            $stored_image = $image->store('/public/ads');
            $url = Storage::url($stored_image);
            $url = env('APP_URL') . $url;
        }
        Advertisement::create([...$advertisementData->toArray(), "image" => $url]);
        return to_route('advertisements.index')->with('success', 'advertisement_created');
    }


    public function destroy(Advertisement $advertisement)
    {
        $advertisement->delete();
        return to_route('advertisements.index')->with('success', 'advertisement_deleted');
    }
}
