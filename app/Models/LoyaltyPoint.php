<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    use HasFactory;

    public $timestamps = false;
    const CREATED_AT = null;

    protected $fillable = [
        'user_id',
        'points',
        'total_earned',
        'total_spent',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'total_earned' => 'integer',
            'total_spent' => 'integer',
            'updated_at' => 'datetime',
        ];
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(LoyaltyTransaction::class, 'user_id', 'user_id');
    }

    // Helper methods
    public function earn($points, $orderId, $description)
    {
        $this->points += $points;
        $this->total_earned += $points;
        $this->save();

        LoyaltyTransaction::create([
            'user_id' => $this->user_id,
            'order_id' => $orderId,
            'type' => 'earned',
            'points' => $points,
            'description' => $description,
            'balance_after' => $this->points,
        ]);

        return $this;
    }

    public function spend($points, $orderId, $description)
    {
        if ($this->points < $points) {
            throw new \Exception('Insufficient loyalty points');
        }

        $this->points -= $points;
        $this->total_spent += $points;
        $this->save();

        LoyaltyTransaction::create([
            'user_id' => $this->user_id,
            'order_id' => $orderId,
            'type' => 'spent',
            'points' => $points,
            'description' => $description,
            'balance_after' => $this->points,
        ]);

        return $this;
    }

    public function expire($points, $description)
    {
        $this->points -= $points;
        $this->save();

        LoyaltyTransaction::create([
            'user_id' => $this->user_id,
            'type' => 'expired',
            'points' => $points,
            'description' => $description,
            'balance_after' => $this->points,
        ]);

        return $this;
    }
}

