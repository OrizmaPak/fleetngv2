<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UiSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:80'],
        ]);

        $term = trim($validated['q']);
        if (strlen($term) < 2) {
            return response()->json([
                'message' => 'Enter at least two non-space characters.',
                'errors' => ['q' => ['Enter at least two non-space characters.']],
            ], 422);
        }

        $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term) . '%';
        $user = $request->user();
        $role = (string) $user->user_type;
        $driverIds = $role === '1' ? null : collect($user->drivers_ids())->values();

        $drivers = Driver::query()
            ->when($driverIds !== null, function ($query) use ($driverIds) {
                return $query->whereIn('id', $driverIds);
            })
            ->where(function ($query) use ($like) {
                $query->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('vehicle_id', 'like', $like);
            })
            ->limit(6)
            ->get(['id', 'first_name', 'last_name', 'vehicle_id'])
            ->map(function ($driver) {
                return [
                    'label' => trim($driver->first_name . ' ' . $driver->last_name),
                    'meta' => $driver->vehicle_id ?: 'Driver',
                    'url' => route('view-driver', $driver->id),
                ];
            });

        $trips = Trip::query()
            ->when($driverIds !== null, function ($query) use ($driverIds) {
                return $query->whereIn('driver_id', $driverIds);
            })
            ->where(function ($query) use ($like, $term) {
                $query->where('client_name', 'like', $like);
                if (ctype_digit($term)) {
                    $query->orWhere('id', (int) $term);
                }
            })
            ->latest('id')
            ->limit(6)
            ->get(['id', 'client_name', 'status'])
            ->map(function ($trip) {
                return [
                    'label' => 'Trip #' . $trip->id,
                    'meta' => $trip->client_name ?: 'Trip record',
                    'url' => route('trip-detail', $trip->id),
                ];
            });

        $clientIds = $role === '1' ? null : collect($user->client_ids())->values();
        $clients = Customer::query()
            ->when($clientIds !== null, function ($query) use ($clientIds) {
                return $query->whereIn('id', $clientIds);
            })
            ->where(function ($query) use ($like) {
                $query->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone_number', 'like', $like);
            })
            ->limit(6)
            ->get(['id', 'first_name', 'last_name', 'phone_number'])
            ->map(function ($client) use ($user) {
                return [
                    'label' => trim($client->first_name . ' ' . $client->last_name),
                    'meta' => $client->phone_number ?: 'Client',
                    'url' => (int) $user->user_type === 1
                        ? route('client-edit', $client->id)
                        : route('client-list'),
                ];
            });

        $people = collect();
        if ($role === '1') {
            $people = User::query()
                ->whereIn('user_type', [3, 4])
                ->where(function ($query) use ($like) {
                    $query->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                })
                ->limit(6)
                ->get(['id', 'first_name', 'last_name', 'user_type'])
                ->map(function ($person) {
                    return [
                        'label' => trim($person->first_name . ' ' . $person->last_name),
                        'meta' => (int) $person->user_type === 4 ? 'Merchant' : 'User',
                        'url' => (int) $person->user_type === 4
                            ? route('view-merchant', $person->id)
                            : route('superadmin-view-user', $person->id),
                    ];
                });
        }

        return response()->json([
            'groups' => [
                'Drivers' => $drivers,
                'Clients' => $clients,
                'Trips' => $trips,
                'Users and merchants' => $people,
            ],
        ]);
    }
}
