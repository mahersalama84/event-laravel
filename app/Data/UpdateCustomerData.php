<?php

namespace App\Data;

use App\Enums\ActiveStatus;
use App\Enums\PrefixCode;
use App\Models\Customer;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Digits;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Prohibited;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpdateCustomerData extends Data
{
  #[Prohibited]
  public string|Uuid|Optional $id;
  public ActiveStatus|Optional $is_active;
  // #[Computed]
  // public string $full_name;   

  public function __construct(
    #[Required, Max(30)]
    public string $first_name,
    #[Required, Max(30)]
    public string $last_name,
    #[Required, Digits(9), Unique('customers', ignore: new RouteParameterReference('customer', 'id'))]
    public string $mobile,
    #[Required, Email(), Unique('customers', ignore: new RouteParameterReference('customer', 'id'))]
    public string $email,
    #[Required, Enum(PrefixCode::class)]
    public string $prefix,
    #[Digits(4), Confirmed]
    public ?string $password,
    #[Max(100)]
    public ?UploadedFile $image,
    #[Date]
    public ?CarbonImmutable $mobile_verified_at
  ) {
    $this->id = Optional::create();
    $this->is_active = ActiveStatus::ACTIVE;
    // $this->full_name = "{$this->first_name} {$this->last_name}";
  }
}
