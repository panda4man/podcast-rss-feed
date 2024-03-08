<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Podcast extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'website_url', 'login_url', 'username', 'password', 'markup_path'];

    public function password(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Crypt::decryptString($value)
        );
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }
}
