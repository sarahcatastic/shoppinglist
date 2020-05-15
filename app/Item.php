<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = ['article', 'description', 'amount', 'maxPrice', 'shopping_list_id'];

    public function shopping_list():BelongsTo {
        return $this->belongsTo(ShoppingList::class);
    }
}
