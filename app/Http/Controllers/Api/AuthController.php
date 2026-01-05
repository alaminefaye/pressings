<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\LoyaltyPoint;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Request OTP code
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $phone = $request->phone;

        // Clean phone number (remove spaces, dashes, etc.)
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        $result = $this->otpService->generateAndSend($phone);

        return response()->json($result);
    }

    /**
     * Verify OTP and login/register
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'code' => 'required|string|size:6',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        $phone = preg_replace('/[^0-9+]/', '', $request->phone);

        // Verify OTP
        $result = $this->otpService->verify($phone, $request->code);

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        // Find or create user
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            // Register new user
            $user = User::create([
                'phone' => $phone,
                'first_name' => $request->first_name ?? 'Client',
                'last_name' => $request->last_name ?? '',
                'role' => 'client',
                'is_active' => true,
                'phone_verified_at' => now(),
            ]);

            // Create wallet
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);

            // Create loyalty points
            LoyaltyPoint::create([
                'user_id' => $user->id,
                'points' => 0,
            ]);
        } else {
            // Update phone verification
            if (!$user->phone_verified_at) {
                $user->phone_verified_at = now();
                $user->save();
            }
        }

        // Generate token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Authentification réussie',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'profile_photo' => $user->profile_photo,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Login with email and password (for admin/employees)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte a été désactivé.',
            ], 403);
        }

        // Generate token
        $token = $user->createToken('web-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'profile_photo' => $user->profile_photo,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'profile_photo' => $user->profile_photo,
                    'is_active' => $user->is_active,
                    'phone_verified_at' => $user->phone_verified_at,
                ],
                'wallet' => $user->wallet,
                'loyalty_points' => $user->loyaltyPoints,
            ],
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'profile_photo' => 'sometimes|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo = $path;
        }

        $user->fill($request->only(['first_name', 'last_name', 'email']));
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'profile_photo' => $user->profile_photo,
                ],
            ],
        ]);
    }
}

