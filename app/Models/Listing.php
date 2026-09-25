<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ListingFactory;

class Listing extends Model
{
    use HasFactory;

    protected static function newFactory(): ListingFactory
    {
        return ListingFactory::new();
    }

    protected $fillable = [
        'title',
        'price',
        'type',
        'bedrooms',
        'location',
        'latitude',
        'longitude',
        'agent_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'bedrooms' => 'integer',
            'agent_id' => 'integer',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
