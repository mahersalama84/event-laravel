<?php

namespace App\Data;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ImageData extends Data
{
    public function __construct(
        #[Mimes('jpg', 'png'),Max(100)]
        public UploadedFile $image,
    ) {
    }
}
