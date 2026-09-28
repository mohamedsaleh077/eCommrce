<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Categorie;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Order;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['category_id', 'name', 'description', 'image_id', 'price', 'discount', 'discount_end', 'stock'])]
#[Hidden(['sold'])]

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

    public function upload(): HasMany
    {
        return $this->HasMany(Upload::class);
    }
}
