<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;

class GeneralController extends Controller
{
    public function advertisements()
    {
        $ads = Advertisement::wherePublished("published")->get()->pluck("image");
        return response()->json($ads, 200);
    }
}
