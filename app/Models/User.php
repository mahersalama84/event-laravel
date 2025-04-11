<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Stats\UserStats;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasUuids, LogsActivity;

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'password',
        'image',
        'show_customers_stats',
        'show_users_stats'
    ];
    protected $appends = ['admin', 'full_name'];

    protected $hidden = [
        'password',
        'remember_token',
        'roles'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('User')
            ->logOnly(['first_name', 'last_name', 'email', 'image']);
    }

    protected function isAdmin(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->roles[0]->name == 'admin',
        );
    }
    public function getAdminAttribute()
    {
        return $this->isAdmin;
    }
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function scopeOrderByFullName($query, $sortType)
    {
        $query->orderBy('first_name', $sortType)->orderBy('last_name', $sortType);
    }

    public function scopeOrderByEmail($query, $sortType)
    {
        $query->orderBy('email', $sortType);
    }

    public function scopeOrderByAll($query, $sortBy, $sortType)
    {
        if ($sortBy == 'email' && $sortType)
            $query->orderBy($sortBy, $sortType);
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

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        })->when($filters['role'] ?? null, function ($query, $role) {
            $query->whereRole($role);
        });
    }

    public static function boot()
    {
        parent::boot();
        static::updating(function ($user) {});
        static::deleting(function ($user) {
            UserStats::decrease();
            if ($user->image) {
                $userImage = str_replace(env('APP_URL') . '/storage', '', $user->image);
                if ($userImage && Storage::exists('/public' . $userImage)) {
                    Storage::delete('/public' . $userImage);
                }
            }
        });
    }
}
