<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;

    // Only created_at is used, not updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'phone',
        'code',
        'expires_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // Helper methods
    public function isExpired()
    {
        return now()->isAfter($this->expires_at);
    }

    public function isVerified()
    {
        return !is_null($this->verified_at);
    }

    public static function generateCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

