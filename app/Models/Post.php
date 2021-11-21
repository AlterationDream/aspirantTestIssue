<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'image', 'link', 'pubDate'
    ];

    public function likedUsers() {
        return $this->belongsToMany(User::class);
    }
}
