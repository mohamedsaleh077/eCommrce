<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['product_id', 'filename', 'description'])]
#[Hidden([])]
class Upload extends Model
{
    public function product(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
