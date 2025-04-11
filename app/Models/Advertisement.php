<?php

namespace App\Models;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Advertisement extends Model
{
    use  HasUuids;
    protected $fillable = ['id', 'image', 'published'];

    public function scopeOrderByDate($query)
    {
        $query->orderBy('created_at', 'desc');
    }

    public function scopeWherePublished($query, $published)
    {
        switch ($published) {
            case "published":
                return $query->where('published', '=', PublishStatus::PUBLISHED);
            case "hidden":
                return $query->where('published', '=', PublishStatus::HIDDEN);
            default:
                return $query;
        }
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['published'] ?? null, function ($query, $published) {
            $query->wherePublished($published);
        });
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($advertisement) {
            if ($advertisement->image) {
                $advertisementImage = str_replace(env('APP_URL') . '/storage', '', $advertisement->image);
                if ($advertisementImage && Storage::exists('/public' . $advertisementImage)) {
                    Storage::delete('/public' . $advertisementImage);
                }
            }
        });
    }
}
