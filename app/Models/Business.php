<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;
    protected $guarded = ["id"];

    public function users():HasMany
    {
        return $this->hasMany(User::class);
    }

    public function locations():HasMany
    {
        return $this->hasMany(Location::class);
    }
}
