<?php

namespace App\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Prohibited;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpdateOccasionData extends Data
{
  #[Prohibited]
  public string|Uuid|Optional $id;
  #[Uuid]
  public ?string $customer_id;
  #[Max(1024)]
  public ?string $description;

  public function __construct(
    #[Required, Max(30)]
    public string $title,
    #[Date, WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
    public CarbonImmutable $start_date,
  ) {
    $this->id = Optional::create();
  }
}
