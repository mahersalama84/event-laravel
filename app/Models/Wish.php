<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Wish extends Model
{
    use HasUuids, LogsActivity;

    protected $fillable = [
        'id',
        'occasion_id',
        'title',
        'description',
        'image'
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Wish')
            ->logOnly(['title', 'description', 'image'])
            ->logOnlyDirty();
    }

    public function occasion(): BelongsTo
    {
        return $this->belongsTo(Occasion::class);
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class)
            ->withPivot('note', 'show')
            ->withTimestamps()
            ->orderBy('customer_wish.created_at', 'desc');
    }

    public static function boot()
    {
        parent::boot();
        static::updating(function ($wish) {});
        static::deleting(function ($wish) {
            if ($wish->image) {
                $wishImage = str_replace(env('APP_URL') . '/storage', '', $wish->image);
                if ($wishImage && Storage::exists('/public' . $wishImage)) {
                    Storage::delete('/public' . $wishImage);
                }
            }
        });
    }
}
