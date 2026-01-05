<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Generate and send OTP code
     */
    public function generateAndSend(string $phone): array
    {
        // Generate OTP code
        $code = OtpCode::generateCode();
        $expiryMinutes = config('otp.expiry_minutes', 5);

        // Save OTP to database
        OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);

        // Send SMS
        $message = "Votre code de vérification est: {$code}. Valide pendant {$expiryMinutes} minutes.";
        $this->sendSms($phone, $message, 'otp');

        return [
            'success' => true,
            'message' => 'Code OTP envoyé avec succès',
            'expires_in' => $expiryMinutes * 60, // in seconds
        ];
    }

    /**
     * Verify OTP code
     */
    public function verify(string $phone, string $code): array
    {
        $otp = OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'Code OTP invalide ou expiré',
            ];
        }

        // Mark as verified
        $otp->verified_at = now();
        $otp->save();

        return [
            'success' => true,
            'message' => 'Code OTP vérifié avec succès',
        ];
    }

    /**
     * Check if OTP is valid
     */
    public function isValid(string $phone, string $code): bool
    {
        return OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Send SMS (to be implemented with actual SMS gateway)
     */
    protected function sendSms(string $phone, string $message, string $type = 'notification'): void
    {
        try {
            // Log SMS
            $smsLog = SmsLog::create([
                'phone' => $phone,
                'message' => $message,
                'type' => $type,
                'status' => 'pending',
            ]);

            // TODO: Implement actual SMS gateway integration
            // For now, just log the message
            Log::info("SMS to {$phone}: {$message}");

            // Simulate successful send
            $smsLog->markAsSent();

            // In production, integrate with SMS providers:
            // - Orange Money SMS API
            // - MTN SMS API
            // - Twilio
            // - Vonage (Nexmo)
            // etc.

        } catch (\Exception $e) {
            Log::error("Failed to send SMS to {$phone}: " . $e->getMessage());
            
            if (isset($smsLog)) {
                $smsLog->markAsFailed([
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Clean up expired OTP codes
     */
    public function cleanupExpired(): int
    {
        return OtpCode::where('expires_at', '<', now()->subHours(24))->delete();
    }
}

