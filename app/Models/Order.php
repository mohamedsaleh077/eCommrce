<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Receipts;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
#[Fillable(['user_id', 'estimated_time', 'arrived_at', 'created_at'])]
#[Hidden([])]
class Order extends Model
{
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
