<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DriverLocation;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    /**
     * Get all deliveries (filtered by role)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Delivery::with(['order.client', 'driver', 'address']);

        // Filter by role
        if ($user->isDriver()) {
            $query->where('driver_id', $user->id);
        } elseif ($user->isClient()) {
            $query->whereHas('order', function ($q) use ($user) {
                $q->where('client_id', $user->id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Date filter
        if ($request->has('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        $deliveries = $query->latest('scheduled_at')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $deliveries,
        ]);
    }

    /**
     * Get delivery details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $query = Delivery::with(['order.client', 'order.items.service', 'order.items.clothingType', 'driver', 'address', 'locations']);

        // Apply role-based access
        if ($user->isDriver()) {
            $query->where('driver_id', $user->id);
        } elseif ($user->isClient()) {
            $query->whereHas('order', function ($q) use ($user) {
                $q->where('client_id', $user->id);
            });
        }

        $delivery = $query->findOrFail($id);

        // Get latest driver location
        $latestLocation = $delivery->locations()->latest()->first();

        return response()->json([
            'success' => true,
            'data' => [
                'delivery' => $delivery,
                'latest_location' => $latestLocation,
            ],
        ]);
    }

    /**
     * Assign driver to delivery (admin/employee only)
     */
    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $delivery = Delivery::with('order')->findOrFail($id);

        // Verify the user is a driver
        $driver = \App\Models\User::findOrFail($request->driver_id);
        if (!$driver->isDriver()) {
            return response()->json([
                'success' => false,
                'message' => 'L\'utilisateur sélectionné n\'est pas un livreur',
            ], 422);
        }

        $delivery->assignToDriver($request->driver_id);

        // TODO: Send notification to driver

        return response()->json([
            'success' => true,
            'message' => 'Livreur assigné avec succès',
            'data' => $delivery->load('driver'),
        ]);
    }

    /**
     * Start delivery (driver only)
     */
    public function start(Request $request, $id)
    {
        $user = $request->user();
        $delivery = Delivery::findOrFail($id);

        // Check if driver is assigned to this delivery
        if ($delivery->driver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas assigné à cette livraison',
            ], 403);
        }

        if ($delivery->status !== 'assigned') {
            return response()->json([
                'success' => false,
                'message' => 'Cette livraison ne peut pas être démarrée',
            ], 422);
        }

        $delivery->start();

        // Update order status
        if ($delivery->type === 'delivery') {
            $delivery->order->updateStatus('in_delivery', $user->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Livraison démarrée',
            'data' => $delivery,
        ]);
    }

    /**
     * Complete delivery (driver only)
     */
    public function complete(Request $request, $id)
    {
        $request->validate([
            'signature' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();
        $delivery = Delivery::with('order')->findOrFail($id);

        // Check if driver is assigned
        if ($delivery->driver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas assigné à cette livraison',
            ], 403);
        }

        if ($delivery->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Cette livraison n\'est pas en cours',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('delivery-proofs', 'public');
            }

            // Complete delivery
            $delivery->complete($request->signature, $photoPath);
            $delivery->notes = $request->notes;
            $delivery->save();

            // Update order status
            if ($delivery->type === 'pickup') {
                $delivery->order->updateStatus('received', $user->id, 'Articles collectés');
            } elseif ($delivery->type === 'delivery') {
                $delivery->order->updateStatus('delivered', $user->id, 'Articles livrés');
            }

            DB::commit();

            // TODO: Send notification to client

            return response()->json([
                'success' => true,
                'message' => 'Livraison complétée avec succès',
                'data' => $delivery->fresh(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la complétion: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update driver location
     */
    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
        ]);

        $user = $request->user();
        $delivery = Delivery::findOrFail($id);

        // Check if driver is assigned
        if ($delivery->driver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        // Create location record
        $location = DriverLocation::create([
            'driver_id' => $user->id,
            'delivery_id' => $delivery->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'speed' => $request->speed,
            'heading' => $request->heading,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Position mise à jour',
            'data' => $location,
        ]);
    }

    /**
     * Get driver's current location
     */
    public function getDriverLocation(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);
        $user = $request->user();

        // Check access (client can only see their own deliveries)
        if ($user->isClient()) {
            if ($delivery->order->client_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé',
                ], 403);
            }
        }

        $latestLocation = $delivery->locations()->latest()->first();

        if (!$latestLocation) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune position disponible',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'location' => $latestLocation,
                'driver' => $delivery->driver,
                'delivery' => [
                    'id' => $delivery->id,
                    'status' => $delivery->status,
                    'type' => $delivery->type,
                    'scheduled_at' => $delivery->scheduled_at,
                ],
            ],
        ]);
    }

    /**
     * Get location history
     */
    public function locationHistory(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);

        $locations = $delivery->locations()
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }

    /**
     * Get driver statistics
     */
    public function driverStatistics(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return response()->json([
                'success' => false,
                'message' => 'Accessible uniquement aux livreurs',
            ], 403);
        }

        $stats = [
            'total_deliveries' => Delivery::where('driver_id', $user->id)->count(),
            'completed_today' => Delivery::where('driver_id', $user->id)
                ->where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
            'pending' => Delivery::where('driver_id', $user->id)
                ->where('status', 'assigned')
                ->count(),
            'in_progress' => Delivery::where('driver_id', $user->id)
                ->where('status', 'in_progress')
                ->count(),
            'completed_total' => Delivery::where('driver_id', $user->id)
                ->where('status', 'completed')
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get available drivers (admin/employee only)
     */
    public function availableDrivers(Request $request)
    {
        $drivers = \App\Models\User::drivers()
            ->active()
            ->withCount(['deliveries as pending_deliveries' => function ($query) {
                $query->whereIn('status', ['assigned', 'in_progress']);
            }])
            ->get()
            ->map(function ($driver) {
                return [
                    'id' => $driver->id,
                    'full_name' => $driver->full_name,
                    'phone' => $driver->phone,
                    'pending_deliveries' => $driver->pending_deliveries,
                    'availability' => $driver->pending_deliveries < 5 ? 'available' : 'busy',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $drivers,
        ]);
    }
}


