<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Location extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function checkins(): HasMany
    {
        return $this->hasMany(Checkin::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function parentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id', 'id');
    }

    public function subLocations(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('business', function (Builder $query) {
            if (auth()->check()) {
                $query->where('business_id', auth()->user()->business_id);
            }
        });
    }
}
