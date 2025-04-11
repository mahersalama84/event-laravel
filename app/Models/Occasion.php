<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Occasion extends Model
{
    use HasUuids, LogsActivity;
    protected $appends = ['attendence_ids', 'attendence_count'];
    protected $fillable = [
        'id',
        'customer_id',
        'title',
        'description',
        'start_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'pivot'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Occasion')
            ->logOnly(['title', 'description', 'start_date'])
            ->logOnlyDirty();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class)->orderby('created_at', 'desc');
    }

    public function attendence(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class)
            ->withTimestamps()
            ->orderBy('customer_occasion.created_at', 'desc');
    }

    public function scopeOrderByAll($query, $sortBy, $sortType)
    {
        if ($sortBy && $sortType)
            $query->orderBy($sortBy, $sortType);
        else
            $query->orderBy('created_at', 'desc');
    }

    public function scopeWhereStartDate($query, $start_date)
    {
        return $query->where('start_date', '>=', $start_date);
        return $query->where('start_date', '>=', $start_date);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhereHas('customer', function ($query1) use ($search) {
                    $query1->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                });
        })->when($filters['start_date'] ?? null, function ($query, $start_date) {
            $query->whereStartDate($start_date);
        });
    }

    public function scopeSearchOccasion($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhereHas('customer', function ($query1) use ($search) {
                    $query1->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('mobile', 'like', '%' . $search . '%');
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

    public static function boot()
    {
        parent::boot();
        static::creating(function ($occasion) {});
        static::updating(function ($occasion) {});
        static::deleting(function ($occasion) {
            foreach ($occasion->wishes as $wish) {
                $wishImage = str_replace(env('APP_URL') . '/storage', '', $wish->image);
                if ($wishImage && Storage::exists('/public' . $wishImage)) {
                    Storage::delete('/public' . $wishImage);
                }
            }
            $occasion->wishes()->delete();
        });
    }
}
