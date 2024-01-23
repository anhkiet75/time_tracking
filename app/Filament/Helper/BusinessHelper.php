<?php

namespace App\Filament\Helper;

use App\Models\BusinessQRCodeRange;
use App\Models\Location;

class BusinessHelper
{
    public static function convertStringToRangesArray(string $input)
    {
        $ranges = explode(',', $input);
        $result = [];

        foreach ($ranges as $range) {
            $rangeParts = explode('-', trim($range));

            if (count($rangeParts) === 2) {
                if (is_numeric($rangeParts[0]) && is_numeric($rangeParts[1])) {
                    $result[] = [
                        'start' => (int)$rangeParts[0],
                        'end' => (int)$rangeParts[1],
                    ];
                }
            } else if (count($rangeParts) === 1 && is_numeric($rangeParts[0])) {
                $result[] = [
                    'start' => (int)$rangeParts[0],
                    'end' => (int)$rangeParts[0],
                ];
            }
        }

        return $result;
    }

    public static function convertRangesToString($ranges)
    {
        $result = [];
        foreach ($ranges as $range) {
            if ($range->start_range === $range->end_range) {
                $result[] = $range->start_range;
            } else {
                $result[] = $range->start_range . '-' . $range->end_range;
            }
        }

        return implode(',', $result);
    }

    public static function mergeRanges(array $ranges)
    {
        // Sort by start key
        usort($ranges, fn($x, $y) => $x['start'] - $y['start']);
        $merged_ranges = [];
        $current_start = null;
        $current_end = null;
        foreach ($ranges as $range) {
            if ($current_start === null) {
                // Initialize the current range
                $current_start = $range['start'];
                $current_end = $range['end'];
            } else {
                // Check if the current range can be extended
                if ($range['start'] <= $current_end + 1) {
                    $current_end = max($current_end, $range['end']);
                } else {
                    // Save the current range and start a new one
                    $merged_ranges[] = ['start' => $current_start, 'end' => $current_end];
                    $current_start = $range['start'];
                    $current_end = $range['end'];
                }
            }
        }
        if ($current_start !== null) {
            $merged_ranges[] = ['start' => $current_start, 'end' => $current_end];
        }
        return $merged_ranges;
    }

    public static function validateRangeUsed(array $ranges)
    {
        $existingRanges = BusinessQRCodeRange::all();
        $usedRanges = [];
        foreach ($ranges as $range) {
            $startRange = $range['start'];
            $endRange = $range['end'];
            if ($startRange > $endRange)
                return ["invalid"];
            foreach ($existingRanges as $existingRange) {
                if (
                    ($startRange >= $existingRange->start_range && $startRange <= $existingRange->end_range) ||
                    ($endRange >= $existingRange->start_range && $endRange <= $existingRange->end_range) ||
                    ($startRange <= $existingRange->start_range && $endRange >= $existingRange->end_range)
                ) {
                    if ($existingRange->start_range == $existingRange->end_range)
                        $usedRanges[] = $existingRange->start_range;
                    else
                        $usedRanges[] = $existingRange->start_range . "-" . $existingRange->end_range;
                }
            }
        }
        return $usedRanges;
    }

    public static function validateRangeInternal($business_id, $qr_code)
    {
        $existingRanges = BusinessQRCodeRange::where('business_id', $business_id)->get();
        foreach ($existingRanges as $existingRange) {
            if ($qr_code >= $existingRange->start_range && $qr_code <= $existingRange->end_range) {
                return true;
            }
        }
        return false;
    }

    public static function validateRangeInternalByRange($business_id, $ranges)
    {
        $existingRanges = BusinessQRCodeRange::where('business_id', $business_id)->get();
        $targetRange = [];
        foreach ($ranges as $range) {
            $startRange = $range['start'];
            $endRange = $range['end'];
            if ($startRange > $endRange)
                return false;
            foreach ($existingRanges as $existingRange) {
                if ($startRange >= $existingRange->start_range && $endRange <= $existingRange->end_range) {
                    $targetRange['start'] = $existingRange->start_range;
                    $targetRange['end'] = $existingRange->end_range;
                }
            }
        }
        return $targetRange;
    }

    public static function getRemovedRange($qrRange, $rangeToRemove)
    {
        $result = [];
        if ($qrRange['start'] == $rangeToRemove['start'] - 1)
            $result[] = $qrRange['start'] . '';
        else if ($qrRange['start'] < $rangeToRemove['start'] - 1)
            $result[] = $qrRange['start'] . "-" . ($rangeToRemove['start'] - 1);

        if ($qrRange['end'] == $rangeToRemove['end'] + 1)
            $result[] = $qrRange['end'] . '';
        else if ($qrRange['end'] > $rangeToRemove['end'] + 1)
            $result[] = ($rangeToRemove['end'] + 1) . "-" . $qrRange['end'];
        return $result;
    }

    public static function getUsedQRCode($start_range, $end_range)
    {
        return Location::withoutGlobalScopes()
            ->whereBetween('qr_code', [$start_range, $end_range])
            ->pluck('qr_code');
    }

    public static function createQRCodeRanges($business_id, $ranges)
    {
        foreach ($ranges as $range) {
            $startRange = $range['start'];
            $endRange = $range['end'];

            BusinessQRCodeRange::create([
                'business_id' => $business_id,
                'start_range' => $startRange,
                'end_range' => $endRange,
            ]);
        }
    }
}
