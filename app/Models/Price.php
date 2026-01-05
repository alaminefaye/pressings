<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'clothing_type_id',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function clothingType()
    {
        return $this->belongsTo(ClothingType::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeForClothingType($query, $clothingTypeId)
    {
        return $query->where('clothing_type_id', $clothingTypeId);
    }
}

