<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Product;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

#[Fillable(['category'])]
#[Hidden([])]
class Categorie extends Model
{
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
