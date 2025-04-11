<?php

namespace App\Data;

use App\Enums\PublishStatus;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Prohibited;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AdvertisementData extends Data
{
  #[Prohibited]
  public string|Uuid|Optional $id;
  public PublishStatus|Optional $published;

  public function __construct(
    #[Required, Mimes('jpg', 'png'), Max(100)]
    public UploadedFile $image,
  ) {
    $this->id = Optional::create();
    $this->published = PublishStatus::HIDDEN;
  }
}
