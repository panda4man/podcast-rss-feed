<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['username', 'podcast', 'enabled'];

    protected $casts = [
        'enabled' => 'boolean'
    ];

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class);
    }
}
