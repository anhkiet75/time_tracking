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

    public function scopeGetAll(Builder $query): void
    {
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->user()->id);
        }
        $query;
    }

    public function scopeThisWeek(Builder $query): void
    {
        if (!auth()->user()->is_admin) {
            $query
                ->where('user_id', auth()->user()->id)
                ->whereBetween('checkin_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        }
        $query;
    }

    public function scopeLastWeek(Builder $query): void
    {
        if (!auth()->user()->is_admin) {
            $query
                ->where('user_id', auth()->user()->id)
                ->whereBetween('checkin_time', [Carbon::now()->subWeek()->startOfWeek()->toDateString(), Carbon::now()->subWeek()->endOfWeek()->toDateString()]);
        }
        $query;
    }

    public function scopeThisMonth(Builder $query): void
    {
        if (!auth()->user()->is_admin) {
            $query
                ->where('user_id', auth()->user()->id)
                ->whereBetween('checkin_time', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()]);
        }
        $query;
    }

    public function scopeThisQuarter(Builder $query): void
    {
        if (!auth()->user()->is_admin) {
            $query
                ->where('user_id', auth()->user()->id)
                ->whereBetween('checkin_time', [Carbon::now()->startOfQuarter()->toDateString(), Carbon::now()->endOfQuarter()->toDateString()]);
        }
        $query;
    }

    public function scopeLastQuarter(Builder $query): void
    {
        if (!auth()->user()->is_admin) {
            $query
                ->where('user_id', auth()->user()->id)
                ->whereBetween('checkin_time', [Carbon::now()->subQuarter()->startOfQuarter()->toDateString(), Carbon::now()->subQuarter()->endOfQuarter()->toDateString()]);
        }
        $query;
    }

    public function scopeLastMonth(Builder $query): void
    {
        if (!auth()->user()->is_admin) {
            $query
                ->where('user_id', auth()->user()->id)
                ->whereBetween('checkin_time', [Carbon::now()->subMonth()->startOfMonth()->toDateString(), Carbon::now()->subMonth()->endOfMonth()->toDateString()]);
        }
        $query;
    }

    public function scopeThisYear(Builder $query): void
    {
        if (!auth()->user()->is_admin) {
            $query
                ->where('user_id', auth()->user()->id)
                ->whereBetween('checkin_time', [Carbon::now()->startOfYear()->toDateString(), Carbon::now()->endOfYear()->toDateString()]);
        }
        $query;
    }
}
