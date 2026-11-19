<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Asset::query();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($category = $request->query('category')) {
            $query->where('category_id', $category);
        }
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('assigned_user', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->paginate(25),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $asset = Asset::find($id);
        if (! $asset) {
            return response()->json(['status' => 'error', 'message' => 'Asset not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $asset,
        ]);
    }

    public function lookup(Request $request): JsonResponse
    {
        $query = $request->input('query');
        if (empty($query)) {
            return response()->json(['status' => 'error', 'message' => 'Query is required'], 400);
        }

        $results = Asset::where('id', 'like', "%{$query}%")
            ->orWhere('serial_number', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $results->count(),
            'data' => $results,
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total' => Asset::count(),
            'assigned' => Asset::where('status', 'Assigned')->count(),
            'available' => Asset::where('status', 'Available')->count(),
            'repair' => Asset::where('status', 'In Repair')->count(),
            'retired' => Asset::where('status', 'Retired')->count(),
        ]);
    }
}
