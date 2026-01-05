<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    use HasFactory;

    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'driver_id',
        'delivery_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'heading',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'accuracy' => 'decimal:2',
            'speed' => 'decimal:2',
            'heading' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    // Relations
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}

