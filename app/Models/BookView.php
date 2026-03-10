<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookView extends Model
{
    use HasFactory;
    protected $fillable = [
        'book_id',
        'user_id',
        'viewed_at'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function Book():HasOne{
        return $this->hasOne(Book::class);
    }
    public function User():HasOne{
        return $this->hasOne(User::class);
    }

}
