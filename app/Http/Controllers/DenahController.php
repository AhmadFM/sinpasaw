<?php

namespace App\Http\Controllers;

use App\Services\Denah\DenahService;
use Illuminate\Contracts\View\View;

/**
 * DenahController - Web Routes untuk Denah
 * Menangani rendering view dan passing data ke frontend
 */
class DenahController extends Controller
{
    public function __construct(private DenahService $denahService) {}

    /**
     * GET /denah
     * Display interactive denah map
     */
    public function index(): View
    {
        // Get tenant data indexed by denah_id untuk inline JSON
        $denahTenants = $this->denahService->getTenantDataByDenahId();
        
        // Get statistics untuk optional display
        $statistics = $this->denahService->getStatistics();
        
        return view('guest.denah', [
            'denahTenants' => $denahTenants,
            'statistics' => $statistics,
        ]);
    }

    /**
     * GET /denah/rute
     * Display 3D navigation view
     */
    public function show3D(): View
    {
        return view('guest.denah-3d');
    }

    /**
     * DEPRECATED: GET /api/denah-data
     * Legacy endpoint - use GET /api/denah/data instead
     */
    public function getDenahData()
    {
        return $this->denahService->getTenantDataByDenahId();
    }
}
