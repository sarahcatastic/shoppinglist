<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = ['text', 'shopping_list_id', 'user_id'];

    public function shopping_list():BelongsTo {
        return $this->belongsTo(ShoppingList::class);
    }

    public function user():BelongsTo {
        return $this->belongsTo(User::class);
    }
}
