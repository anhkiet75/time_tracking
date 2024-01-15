<?php

namespace App\Filament\Helper;

use App\Models\BusinessQRCodeRange;

class BusinessHelper
{
    public static function convertInputToRangesArray($input)
    {
        $ranges = explode(',', $input);
        $result = [];

        foreach ($ranges as $range) {
            $rangeParts = explode('-', trim($range));

            if (count($rangeParts) === 2 && is_numeric($rangeParts[0]) && is_numeric($rangeParts[1])) {
                $result[] = [
                    'start' => (int)$rangeParts[0],
                    'end' => (int)$rangeParts[1],
                ];
            }
        }

        return $result;
    }

    public static function validateRange($ranges)
    {
        $existingRanges = BusinessQRCodeRange::all();
        foreach ($ranges as $range) {
            $startRange = $range['start'];
            $endRange = $range['end'];

            foreach ($existingRanges as $existingRange) {
                if (
                    ($startRange >= $existingRange->start_range && $startRange <= $existingRange->end_range) ||
                    ($endRange >= $existingRange->start_range && $endRange <= $existingRange->end_range)
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function validateRangeFrontend($existingRanges, $ranges) {
        foreach ($ranges as $range) {
            $startRange = $range['start'];
            $endRange = $range['end'];

            foreach ($existingRanges as $existingRange) {
                $existingRange = self::convertInputToRangesArray($existingRange);
                if (
                    ($startRange >= $existingRange['start'] && $startRange <= $existingRange['end']) ||
                    ($endRange >= $existingRange['start'] && $endRange <= $existingRange['end'])
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function validateRangeInternal($businessId, $qr_code)
    {
        $existingRanges = BusinessQRCodeRange::where('business_id', $businessId)->get();
        foreach ($existingRanges as $existingRange) {
            if ($qr_code >= $existingRange->start_range && $qr_code <= $existingRange->end_range) {
                return true;
            }
        }
        return false;
    }

    public static function createQRCodeRanges($businessId, $ranges)
    {
        foreach ($ranges as $range) {
            $startRange = $range['start'];
            $endRange = $range['end'];

            BusinessQRCodeRange::create([
                'business_id' => $businessId,
                'start_range' => $startRange,
                'end_range' => $endRange,
            ]);
        }
    }
}
