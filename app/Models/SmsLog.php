<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'phone',
        'message',
        'type',
        'status',
        'provider_response',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'provider_response' => 'array',
            'sent_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Helper methods
    public function markAsSent($response = null)
    {
        $this->status = 'sent';
        $this->sent_at = now();
        if ($response) {
            $this->provider_response = $response;
        }
        $this->save();
    }

    public function markAsFailed($response = null)
    {
        $this->status = 'failed';
        if ($response) {
            $this->provider_response = $response;
        }
        $this->save();
    }
}

