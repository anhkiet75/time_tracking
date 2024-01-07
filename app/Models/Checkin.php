<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkin extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function scopeThisWeek(Builder $query): void
    {
        $query->whereBetween('checkin_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function scopeLastWeek(Builder $query): void
    {
        $query->whereBetween('checkin_time', [Carbon::now()->subWeek()->startOfWeek()->toDateString(), Carbon::now()->subWeek()->endOfWeek()->toDateString()]);
    }

    public function scopeThisMonth(Builder $query): void
    {
        $query->whereBetween('checkin_time', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()]);
    }

    public function scopeThisQuarter(Builder $query): void
    {
        $query->whereBetween('checkin_time', [Carbon::now()->startOfQuarter()->toDateString(), Carbon::now()->endOfQuarter()->toDateString()]);
    }

    public function scopeLastQuarter(Builder $query): void
    {
        $query->whereBetween('checkin_time', [Carbon::now()->subQuarter()->startOfQuarter()->toDateString(), Carbon::now()->subQuarter()->endOfQuarter()->toDateString()]);
    }

    public function scopeLastMonth(Builder $query): void
    {
        $query->whereBetween('checkin_time', [Carbon::now()->subMonth()->startOfMonth()->toDateString(), Carbon::now()->subMonth()->endOfMonth()->toDateString()]);
    }

    public function scopeThisYear(Builder $query): void
    {
        $query->whereBetween('checkin_time', [Carbon::now()->startOfYear()->toDateString(), Carbon::now()->endOfYear()->toDateString()]);
    }
}
