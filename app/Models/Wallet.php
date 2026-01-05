<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Helper methods
    public function credit($amount, $description, $reference = null)
    {
        $this->balance += $amount;
        $this->save();

        WalletTransaction::create([
            'wallet_id' => $this->id,
            'type' => 'credit',
            'amount' => $amount,
            'description' => $description,
            'reference' => $reference,
            'balance_after' => $this->balance,
        ]);

        return $this;
    }

    public function debit($amount, $description, $reference = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $this->balance -= $amount;
        $this->save();

        WalletTransaction::create([
            'wallet_id' => $this->id,
            'type' => 'debit',
            'amount' => $amount,
            'description' => $description,
            'reference' => $reference,
            'balance_after' => $this->balance,
        ]);

        return $this;
    }

    public function hasBalance($amount)
    {
        return $this->balance >= $amount;
    }
}

