<?php

namespace App\Data;

use App\Enums\ActiveStatus;
use App\Enums\PrefixCode;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Digits;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Prohibited;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CustomerData extends Data
{
  #[Prohibited]
  public string|Uuid|Optional $id;
  public ActiveStatus|Optional $is_active;

  public function __construct(
    #[Required, Max(61)]
    public string $full_name,
    #[Required, Max(30)]
    public string $first_name,
    #[Required, Max(30)]
    public string $last_name,
    #[Required, Digits(9)]
    public string $mobile,
    #[Required, Email()]
    public string $email,
    #[Required, Enum(PrefixCode::class)]
    public string $prefix,
    #[Max(100)]
    public ?string $image,
    #[Date]
    public ?CarbonImmutable $mobile_verified_at
  ) {
    $this->id = Optional::create();
    $this->is_active = ActiveStatus::INACTIVE;
  }
}
