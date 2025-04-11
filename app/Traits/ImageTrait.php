<?php

namespace App\Traits;

use App\Data\ImageData;
use Illuminate\Support\Facades\Storage;

trait ImageTrait
{
    public function uploadFile(ImageData $imageData, $model, $dir)
    {
        $modelImage = str_replace(env('APP_URL') . '/storage', '', $model->image);
        if (Storage::exists('/public' . $modelImage) && $modelImage != '/' . $model . '.png') {
            Storage::delete('/public' . $modelImage);
        }
        $image = $imageData->image;
        $stored_image = $image->store('/public/' . $dir);
        $url = Storage::url($stored_image);
        $url = env('APP_URL') . $url;
        return $url;
    }
    public function deleteFile($model, $dir)
    {
        $modelImage = str_replace(env('APP_URL') . '/storage', '', $model->image);
        if (Storage::exists('/public' . $modelImage) && $modelImage != '' && $modelImage != '/' . $model . '.png') {
            Storage::delete('/public' . $modelImage);
        } else {
            return response()->json([
                'message' => trans($dir . ".no_image_found")
            ], 404);
        }
    }
}
