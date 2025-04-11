<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Stats\CustomerStats;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use YieldStudio\LaravelExpoNotifier\Models\ExpoToken;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasUuids, LogsActivity;
    protected $guard_name = 'customers';

    protected $fillable = [
        'id',
        'is_active',
        'first_name',
        'last_name',
        'prefix',
        'mobile',
        'email',
        'password',
        'image',
    ];

    protected $appends = [
        'full_name',
        'mobile_no',
        'attendence_ids',
        'attendence_count',
        'followings_ids',
        'followers_ids',
        'followings_count',
        'followers_count',
        'accepted_followings_ids',
        'accepted_followers_ids'
    ];

    protected $hidden = [
        'password',
        'mobile_verified_at',
        'created_at',
        'updated_at',
        'roles',
        'pivot'
    ];

    protected function casts(): array
    {
        return [
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Customer')
            ->logOnly(['is_active', 'first_name', 'last_name', 'prefix', 'mobile', 'email', 'image'])
            ->logOnlyDirty();
    }

    public function expoToken(): HasOne
    {
        return $this->HasOne(ExpoToken::class, 'owner_id', 'id');
    }

    public function occasions(): HasMany
    {
        return $this->hasMany(Occasion::class);
    }

    public function followingsoccasions(): HasManyThrough
    {
        return $this->hasManyThrough(Occasion::class, Follower::class, 'follower_id', 'customer_id', 'id', 'following_id')
            ->where('followers.accepted', 1)
            ->orderby('occasions.created_at', 'desc');
    }

    public function wishes(): HasManyThrough
    {
        return $this->hasManyThrough(Wish::class, Occasion::class)->orderby('wishes.created_at', 'desc');
    }

    public function bookedWishes(): BelongsToMany
    {
        return $this->belongsToMany(Wish::class)
            ->withPivot('note', 'show')
            ->withTimestamps()
            ->with('occasion')
            ->orderBy('customer_wish.created_at', 'desc');
    }

    public function visibleWishes(): BelongsToMany
    {
        return $this->belongsToMany(Wish::class)
            ->withPivot('note', 'show')
            ->wherePivot('show', '=', 1)
            ->withTimestamps()
            ->with('occasion')
            ->orderBy('customer_wish.created_at', 'desc');
    }

    public function hiddenWishes(): BelongsToMany
    {
        return $this->belongsToMany(Wish::class)
            ->withPivot('note', 'show')
            ->wherePivot('show', '=', 0)
            ->withTimestamps()
            ->with('occasion')
            ->orderBy('customer_wish.created_at', 'desc');
    }

    public function attendence(): BelongsToMany
    {
        return $this->belongsToMany(Occasion::class)
            ->withTimestamps()
            ->orderBy('customer_occasion.created_at', 'desc');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'followers', 'following_id', 'follower_id')
            ->withTimestamps()
            ->orderBy('followers.created_at', 'desc');
    }

    public function acceptedFollowers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'followers', 'following_id', 'follower_id')
            ->wherePivot('accepted', 1);
    }

    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'followers', 'follower_id', 'following_id')
            ->withTimestamps()
            ->orderBy('followers.created_at', 'desc');
    }

    public function acceptedFollowings(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'followers', 'follower_id', 'following_id')
            ->wherePivot('accepted', 1);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function scopeOrderByFullName($query, $sortType)
    {
        $query->orderBy('first_name', $sortType)->orderBy('last_name', $sortType);
    }

    public function getMobileNoAttribute()
    {
        $parts = str_split($this->mobile, 3);
        $formatted_mobile_number = implode("-", $parts);
        return '+' . $this->prefix . ' ' . $formatted_mobile_number;
    }

    public function scopeOrderByMobileNo($query, $sortType)
    {
        $query->orderBy('prefix', $sortType)->orderBy('mobile', $sortType);
    }

    public function scopeOrderByMobile($query, $sortType)
    {
        $query->orderBy('mobile', $sortType);
    }

    public function scopeOrderByEmail($query, $sortType)
    {
        $query->orderBy('email', $sortType);
    }

    public function scopeOrderByAll($query, $sortBy, $sortType)
    {
        if ($sortBy == 'email' && $sortType)
            $query->orderBy('email', $sortType);
        else if ($sortBy == 'mobile_no' && $sortType)
            $query->orderBy('prefix', $sortType)->orderBy('mobile', $sortType);
        else if ($sortBy == 'full_name' && $sortType)
            $query->orderBy('first_name', $sortType)->orderBy('last_name', $sortType);
        else
            $query->orderBy('created_at', 'desc');
    }

    public function scopeWhereRole($query, $role)
    {
        switch ($role) {
            case 'admin':
                return $query->whereHas('roles', function ($roles) {
                    $roles->where('name', 'admin');
                });
            case 'customer':
                return $query->whereHas('roles', function ($roles) {
                    $roles->where('name', 'customer');
                });
        }
    }

    public function scopeWhereIsActive($query, $is_active)
    {
        switch ($is_active) {
            case "active":
                return $query->where('is_active', '=', ActiveStatus::ACTIVE);
            case "inactive":
                return $query->where('is_active', '=', ActiveStatus::INACTIVE);
            default:
                return $query;
        }
    }

    public function scopeWherePrefix($query, $prefix)
    {

        return $query->where('prefix', '=', $prefix);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        })->when($filters['role'] ?? null, function ($query, $role) {
            $query->whereRole($role);
        })->when($filters['is_active'] ?? null, function ($query, $is_active) {
            $query->whereIsActive($is_active);
        })->when($filters['prefix'] ?? null, function ($query, $prefix) {
            $query->wherePrefix($prefix);
        });
    }

    public function scopeSearchCustomer($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        });
    }

    public function getAttendenceIdsAttribute()
    {
        return $this->attendence()->pluck('occasion_id');
    }

    public function getAttendenceCountAttribute()
    {
        return $this->attendence()->count();
    }

    public function getFollowingsIdsAttribute()
    {
        return $this->followings()->pluck('following_id');
    }

    public function getFollowersIdsAttribute()
    {
        return $this->followers()->pluck('follower_id');
    }

    public function getFollowingsCountAttribute()
    {
        return $this->followings()->count();
    }

    public function getFollowersCountAttribute()
    {
        return $this->followers()->count();
    }

    public function getAcceptedFollowingsIdsAttribute()
    {
        return $this->followings()->wherePivot('accepted', 1)->pluck('following_id');
    }

    public function getAcceptedFollowersIdsAttribute()
    {
        return $this->followers()->wherePivot('accepted', 1)->pluck('follower_id');
    }

    public static function boot()
    {
        parent::boot();
        static::updating(function ($customer) {});
        static::deleting(function ($customer) {
            CustomerStats::decrease();
            if ($customer->image) {
                $customerImage = str_replace(env('APP_URL') . '/storage', '', $customer->image);
                if ($customerImage && Storage::exists('/public' . $customerImage)) {
                    Storage::delete('/public' . $customerImage);
                }
            }
        });
    }
}
