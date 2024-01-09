<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessQRCodeRange extends Model
{
    use HasFactory;

    protected $fillable = ["start_range", "end_range", "business_id"];

    protected $table = 'business_qr_code_ranges';

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
