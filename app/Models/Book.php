<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'author',
        'total_quantity',
        'available_quantity',
        'is_active',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function Category():BelongsTo{
        return $this->belongsTo(Category::class);
    }
    public function Views():HasMany{
        return $this->hasMany(BookView::class);
    }
}
