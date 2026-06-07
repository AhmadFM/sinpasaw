{{-- resources/views/guest/denah.blade.php - Using Unified CSS --}}
@extends('layouts.guest')

@section('title', 'Denah Pasar – Pasar Modern Sinpasa')

@section('styles')
@vite(['resources/css/denah-unified.css'])
@endsection

@section('content')

{{-- ── Page header ── --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-4 text-center">
    <h1 class="font-manrope font-black text-2xl lg:text-4xl text-[#003B1F] mb-2"
        style="font-family:'Manrope',sans-serif;">Denah Pasar</h1>
    <p class="text-gray-500 text-sm lg:text-base max-w-lg mx-auto">
        Gunakan denah interaktif kami untuk menemukan tenant yang dicari lebih cepat.
    </p>
</div>

{{-- ── Zoom controls ── --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-3">
    <x-denah.zoom-controls />
</div>

{{-- ── Denah map container ── --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
    <div class="denah-wrap" id="denahContainer">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 2124 1024" fill="none" id="denahSvg">
            @include('components.guest.denah-svg')
            <polyline id="dijkstraPath" points="" stroke="#00AAFF" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" fill="none" style="filter: drop-shadow(0px 0px 8px #00AAFF); opacity: 0.9; pointer-events: none;" />
        </svg>
    </div>
</div>

{{-- ── Legend / Filter ── --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
    <x-denah.legend-filter />
</div>

{{-- ── Info card popup (click on lapak) ── --}}
<x-denah.info-card />

{{-- ── Tenant data dari database (JSON untuk JS) ── --}}
<script id="tenantData" type="application/json">
{!! json_encode($denahTenants) !!}
</script>

@endsection

@section('scripts')
@vite(['resources/js/denah-main.js'])

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const { initDenahMap } = await import('/resources/js/denah-main.js');
        const tenantData = JSON.parse(document.getElementById('tenantData').textContent || '{}');
        initDenahMap(tenantData);
    });
</script>
@endsection
