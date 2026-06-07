<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Denah\DenahCollectionResource;
use App\Http\Resources\Denah\DenahResource;
use App\Http\Resources\Denah\RouteResponse;
use App\Http\Resources\Denah\StatisticsResource;
use App\Services\Denah\DenahService;
use App\Services\Denah\RoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Controller untuk Denah
 * Menangani request denah dan routing
 */
class DenahApiController extends Controller
{
    public function __construct(
        private DenahService $denahService,
        private RoutingService $routingService
    ) {}

    /**
     * GET /api/denah
     * Get semua denah dengan tenant info
     */
    public function index(): JsonResponse
    {
        $denah = $this->denahService->getAllDenah();
        
        return response()->json([
            'success' => true,
            'data' => DenahResource::collection($denah),
        ]);
    }

    /**
     * GET /api/denah/list
     * Get formatted denah list
     */
    public function list(): JsonResponse
    {
        $denah = $this->denahService->getFormattedDenahList();
        
        return response()->json([
            'success' => true,
            'data' => $denah,
            'total' => count($denah),
        ]);
    }

    /**
     * GET /api/denah/data
     * Get complete denah data dengan tenant indexed by denah_id
     * Format untuk frontend (legacy compatibility)
     */
    public function data(): JsonResponse
    {
        $tenantData = $this->denahService->getTenantDataByDenahId();
        
        return response()->json($tenantData);
    }

    /**
     * GET /api/denah/{id}
     * Get single denah by ID
     */
    public function show(string $id): JsonResponse
    {
        $denah = $this->denahService->getDenahById($id);
        
        if (!$denah) {
            return response()->json([
                'success' => false,
                'message' => 'Denah not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new DenahResource($denah),
        ]);
    }

    /**
     * GET /api/denah/blok/{blok}
     * Get all denah by blok
     */
    public function byBlok(string $blok): JsonResponse
    {
        $denah = $this->denahService->getDenahByBlok($blok);
        
        return response()->json([
            'success' => true,
            'data' => DenahResource::collection($denah),
            'total' => $denah->count(),
        ]);
    }

    /**
     * GET /api/denah/search/status/{status}
     * Search denah by status (terisi/kosong)
     */
    public function searchByStatus(string $status): JsonResponse
    {
        $denah = $this->denahService->searchByStatus($status);
        
        return response()->json([
            'success' => true,
            'data' => DenahResource::collection($denah),
            'total' => $denah->count(),
            'status_filter' => $status,
        ]);
    }

    /**
     * POST /api/denah/route
     * Calculate shortest path between two nodes
     * Request body: { start_node_id, end_node_id, include_coordinates: bool }
     */
    public function calculateRoute(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_node_id' => 'required|string',
            'end_node_id' => 'required|string',
            'include_coordinates' => 'boolean',
        ]);

        // Get graph data
        $graphData = $this->denahService->extractGraphData();
        
        if (empty($graphData['nodes']) || empty($graphData['edges'])) {
            return response()->json([
                'success' => false,
                'message' => 'Graph data not available',
            ], 400);
        }

        $startNodeId = $validated['start_node_id'];
        $endNodeId = $validated['end_node_id'];
        $includeCoordinates = $validated['include_coordinates'] ?? false;

        // Calculate path
        if ($includeCoordinates) {
            $path = $this->routingService->findPathWithCoordinates(
                $graphData['nodes'],
                $graphData['edges'],
                $startNodeId,
                $endNodeId
            );
        } else {
            $path = $this->routingService->findShortestPath(
                $graphData['nodes'],
                $graphData['edges'],
                $startNodeId,
                $endNodeId
            );
        }

        // Calculate distance
        $distance = $this->routingService->calculateRouteDistance(
            $graphData['nodes'],
            $graphData['edges'],
            $startNodeId,
            $endNodeId
        );

        return response()->json([
            'success' => true,
            'data' => [
                'path' => $path,
                'distance' => round($distance, 2),
                'node_count' => count($path),
                'start_node_id' => $startNodeId,
                'end_node_id' => $endNodeId,
            ],
        ]);
    }

    /**
     * GET /api/denah/statistics
     * Get marketplace statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = $this->denahService->getStatistics();
        
        return response()->json([
            'success' => true,
            'data' => new StatisticsResource($stats),
        ]);
    }

    /**
     * POST /api/denah/invalidate-cache
     * Invalidate denah cache (admin only)
     */
    public function invalidateCache(): JsonResponse
    {
        $this->denahService->invalidateCache();
        
        return response()->json([
            'success' => true,
            'message' => 'Cache invalidated',
        ]);
    }
}
