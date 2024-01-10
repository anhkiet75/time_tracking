<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Business extends Model
{
    use HasFactory;
    protected $guarded = ["id"];

    public function user(): HasOne
    {
        return $this->hasONe(User::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function qrRanges(): HasMany
    {
        return $this->hasMany(BusinessQRCodeRange::class);
    }

    public static function convertInputToRangesArray($input)
    {
        $ranges = explode(',', $input);
        $result = [];

        foreach ($ranges as $range) {
            $pattern = '/\[(\d+)-(\d+)\]/';
            preg_match($pattern, $range, $matches);

            if (count($matches) === 3) {
                $result[] = [
                    'start' => (int)$matches[1],
                    'end' => (int)$matches[2],
                ];
            }
        }

        return $result;
    }

    public static function assignQRCodeRanges($businessId, $ranges)
    {
        foreach ($ranges as $range) {
            $startRange = $range['start'];
            $endRange = $range['end'];

            self::validateAndStoreRange($businessId, $startRange, $endRange);
        }
    }

    private static function validateAndStoreRange($businessId, $startRange, $endRange)
    {
        $existingRanges = BusinessQRCodeRange::where('business_id', $businessId)->get();

        foreach ($existingRanges as $existingRange) {
            if (
                ($startRange >= $existingRange->start_range && $startRange <= $existingRange->end_range) ||
                ($endRange >= $existingRange->start_range && $endRange <= $existingRange->end_range)
            ) {
                // Overlapping range found, throw validation exception
                abort(422, 'QR Code range overlaps with existing range.');
            }
        }

        // No overlap, store the range
        // self::create([
        //     'business_id' => $businessId,
        //     'start_range' => $startRange,
        //     'end_range' => $endRange,
        // ]);
    }

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $id = isset($model->id) ? $model->id : 0;
            self::assignQRCodeRanges($id, self::convertInputToRangesArray($model->business_range));
        });
    }
}
