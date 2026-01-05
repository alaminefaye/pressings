<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'driver_id',
        'type',
        'address_id',
        'scheduled_at',
        'started_at',
        'completed_at',
        'status',
        'signature',
        'photo',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // Relations
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function locations()
    {
        return $this->hasMany(DriverLocation::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper methods
    public function assignToDriver($driverId)
    {
        $this->driver_id = $driverId;
        $this->status = 'assigned';
        $this->save();
    }

    public function start()
    {
        $this->status = 'in_progress';
        $this->started_at = now();
        $this->save();
    }

    public function complete($signature = null, $photo = null)
    {
        $this->status = 'completed';
        $this->completed_at = now();
        if ($signature) {
            $this->signature = $signature;
        }
        if ($photo) {
            $this->photo = $photo;
        }
        $this->save();
    }
}

