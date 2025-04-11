<?php

namespace App\Data;

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
class WishData extends Data
{
    #[Prohibited]
    public string|Uuid|Optional $id;  
    #[Max(1024)]
    public ?string $description;   
 

    public function __construct(
      #[Required, Max(30)]
      public string $title,
      #[Required, Uuid]
      public string $occasion_id,    
      #[ Mimes('jpg', 'png'),Max(100)]
      public ?UploadedFile $image,           
    ) {
      $this->id = Optional::create();
    }
}
