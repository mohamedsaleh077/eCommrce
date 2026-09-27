<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Categorie;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Order;

class Product extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    } 

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
}
