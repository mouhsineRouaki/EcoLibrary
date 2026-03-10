<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Book;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function Books():HasMany{
        return $this->hasMany(Book::class);
    }

}
