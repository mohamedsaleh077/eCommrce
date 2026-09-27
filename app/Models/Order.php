<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Receipts;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
}
