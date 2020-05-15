<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoppingList extends Model
{

    public $table = "shopping_lists";

    protected $fillable = ['id', 'shopping_date', 'shopping_price', 'status', 'creator_id', 'volunteer_id'];

    // TODO Abfrage, wer Ersteller ist & wer Bearbeiter ist

    public function comments():HasMany {
        return $this->hasMany(Comment::class);
    }

    public function creator():BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function volunteer():BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function items():HasMany {
        return $this->hasMany(Item::class);
    }
}
