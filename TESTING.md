# Denah Refactor - Testing & Validation Guide

## Testing Checklist

### 1. Backend API Testing

#### DenahService Tests
- [ ] `getAllDenah()` returns Eloquent Collection with tenant relationships
- [ ] Cache is applied and TTL works
- [ ] `getFormattedDenahList()` returns properly formatted array
- [ ] `getTenantDataByDenahId()` returns correctly indexed data
- [ ] `getDenahById()` returns single record or null
- [ ] `getDenahByTenantId()` returns correct denah
- [ ] `getDenahByBlok()` filters by blok correctly
- [ ] `searchByStatus()` filters by status ('terisi'/'kosong')
- [ ] `getStatistics()` calculates correct totals and fill rate
- [ ] `invalidateCache()` clears all cached data

#### RoutingService Tests
- [ ] `findShortestPath()` returns correct path array
- [ ] Returns empty array for invalid nodes
- [ ] Early stopping works when target is reached
- [ ] `findPathWithCoordinates()` includes x, y in response
- [ ] `calculateRouteDistance()` returns correct distance value
- [ ] Dijkstra algorithm handles bidirectional edges correctly
- [ ] Adjacency list builds without duplicates

#### API Routes Tests
```bash
# Test each endpoint
GET    /api/denah              # Should return all denah with tenant
GET    /api/denah/list         # Should return formatted list with total
GET    /api/denah/data         # Legacy - should return indexed tenant data
GET    /api/denah/{id}         # Should return single denah or 404
GET    /api/denah/blok/{blok}  # Should return filtered denah
GET    /api/denah/search/status/{status}  # Should filter by status
POST   /api/denah/route        # Should calculate and return route
  Payload: { start_node_id, end_node_id, include_coordinates }
  Response: { success, data: { path, distance, node_count, ... } }
GET    /api/denah/statistics   # Should return fill rate stats
```

### 2. Frontend JS Testing

#### Module Tests
- [ ] **EventBus**: on(), emit(), once() work correctly
- [ ] **SVGRenderer**: parseNodes() extracts all circles
- [ ] **SVGRenderer**: getInteractiveLapaks() finds all clickable elements
- [ ] **SVGRenderer**: drawRoute() renders polyline correctly
- [ ] **SVGRenderer**: screenToSvgCoords() transforms coordinates
- [ ] **RouteCalculator**: API call succeeds with valid parameters
- [ ] **RouteCalculator**: Handles errors gracefully
- [ ] **NavigationManager**: State transitions work
- [ ] **DenahMap**: All modules initialize without errors
- [ ] **DenahMap**: Click event listeners attached to all lapaks

#### User Interaction Tests
- [ ] Click on lapak opens info card
- [ ] Info card displays correct tenant data
- [ ] "Jadikan Ini Titik Awal" button sets start node
- [ ] Zoom in/out/reset buttons work
- [ ] Legend filter dimming applies correctly
- [ ] Route preview updates when start/target changes
- [ ] Info card closes when clicking outside
- [ ] Info card close button works

#### Integration Tests
- [ ] 2D map loads without errors
- [ ] Tenant data JSON parses correctly
- [ ] API calls use correct CSRF token
- [ ] Route calculation completes in reasonable time
- [ ] Can navigate to 3D from 2D
- [ ] Session storage passes graph data to 3D

### 3. 3D Navigation Testing

#### Scene Tests
- [ ] Three.js scene initializes
- [ ] Canvas renders without errors
- [ ] Lighting illuminates the scene
- [ ] Floor renders correctly
- [ ] Player model loads (or graceful fallback)
- [ ] Camera follows player position

#### Navigation Tests
- [ ] W/A/S/D keys move player
- [ ] Route is visualized as blue line
- [ ] Target name displays in UI overlay
- [ ] Reset camera button positions view correctly
- [ ] Gyro button (mobile) appears on touch devices
- [ ] Return button navigates back to 2D

### 4. View & Styling Tests

#### Blade Components
- [ ] `components/denah/info-card.blade.php` renders
- [ ] `components/denah/legend-filter.blade.php` renders
- [ ] `components/denah/zoom-controls.blade.php` renders
- [ ] All components use correct IDs/classes
- [ ] denah.blade.php includes all components

#### CSS Tests
- [ ] denah-unified.css loads without errors
- [ ] Responsive design works on mobile (< 768px)
- [ ] Info card animation smooth
- [ ] Legend items highlight on active
- [ ] Zoom buttons are interactive
- [ ] 3D page background is dark
- [ ] Print styles hide interactive elements

### 5. Cross-Browser Testing

- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] Mobile browsers (iOS Safari, Chrome Mobile)

### 6. Performance Testing

#### Load Times
- [ ] 2D map loads in < 2s
- [ ] API /api/denah/list responds in < 500ms
- [ ] Route calculation completes in < 1s
- [ ] 3D scene renders at 60 FPS

#### Memory
- [ ] Cache doesn't grow indefinitely
- [ ] No memory leaks in event listeners
- [ ] 3D scene cleanup on page unload

#### Network
- [ ] Reduced duplicate API calls
- [ ] Proper caching headers
- [ ] CSRF token included in POST requests

### 7. Accessibility Testing

- [ ] All buttons have aria-labels
- [ ] Canvas has aria-label
- [ ] Keyboard navigation works (W/A/S/D)
- [ ] Color contrast meets WCAG standards
- [ ] prefers-reduced-motion respected
- [ ] prefers-contrast:more supported

## Manual Testing Script

### Test 2D Navigation
```javascript
// Open browser console on /denah
denahMapInstance              // Should exist
denahMapInstance.navigation   // Check state
denahMapInstance.router       // Check router instance
// Click a lapak → info card should show
// Click "Jadikan Titik Awal" → txtStartNode should update
// Zoom in/out → map should scale
// Click legend → lapaks should dim
```

### Test API Directly
```bash
# Test each endpoint
curl http://localhost:8000/api/denah
curl http://localhost:8000/api/denah/list
curl http://localhost:8000/api/denah/data
curl http://localhost:8000/api/denah/statistics
curl -X POST http://localhost:8000/api/denah/route \
  -H "Content-Type: application/json" \
  -d '{"start_node_id":"node-KB001","end_node_id":"node-KB002","include_coordinates":true}'
```

### Test 3D Navigation
```javascript
// On /denah/rute page
denahNav3D                    // Should exist
denahNav3D.route3D            // Should have path
// Move with W/A/S/D
// Camera should follow
// Route should be visible as blue line
```

## Performance Benchmarks

| Metric | Target | Actual |
|--------|--------|--------|
| 2D load time | < 2s | - |
| API list response | < 500ms | - |
| Route calculation | < 1s | - |
| 3D render FPS | 60 | - |
| Cache hit rate | > 80% | - |

## Automated Test Commands

```bash
# Run PHP unit tests
php artisan test

# Run Laravel feature tests for routes
php artisan test --filter=api

# Run linter
./vendor/bin/pint

# Check TypeScript/JSDoc
npm run lint

# Build assets
npm run build
```

## Known Limitations & TODOs

- [ ] Graph data extraction from SVG not yet implemented (buildGraphData() returns empty)
- [ ] Mobile gyro sensor integration (placeholder only)
- [ ] Joystick controls for mobile (UI present, functionality pending)
- [ ] Real-time player tracking/proximity detection
- [ ] Multi-user navigation (no real-time sync)
- [ ] Offline mode support
- [ ] Analytics tracking

## Sign-Off

Testing completed: _______________
Date: _______________
Tested by: _______________

All critical features: ✓ PASS / ✗ FAIL
