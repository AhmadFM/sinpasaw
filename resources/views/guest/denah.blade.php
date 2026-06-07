{{-- resources/views/guest/denah.blade.php - Refactored --}}
@extends('layouts.guest')

@section('title', 'Denah Pasar – Pasar Modern Sinpasa')

@section('styles')
@vite(['resources/css/denah.css'])
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
    <div class="flex items-center justify-end gap-2">
        <button id="btn-zoom-in" class="zoom-btn">+ Perbesar</button>
        <button id="btn-zoom-reset" class="zoom-btn">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Reset
        </button>
        <button id="btn-zoom-out" class="zoom-btn">− Perkecil</button>
    </div>
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
    <div class="flex flex-wrap gap-2">
        @foreach ([
            ['all', '#374151', 'Semua Lapak'],
            ['kios-besar', '#4B7AA8', 'Kios Besar'],
            ['kios-kecil', '#2C85B1', 'Kios Kecil'],
            ['kios-fnb', '#589A67', 'Kios F&B/Kuliner'],
            ['lapak-sayur-buah-dan-jajanan', '#25C54E', 'Lapak Sayur & Buah'],
            ['lapak-non-halal', '#C36D8A', 'Lapak Non-Halal'],
            ['lapak-basah', '#DED24D', 'Lapak Basah'],
            ['lapak-olahan-dan-jajanan', '#EB8946', 'Lapak Olahan & Jajanan'],
            ['lapak-kuliner', '#FF4F3B', 'Pojok Kuliner'],
            ['galeri-dekranasda', '#ABA08E', 'Galeri Dekranasda'],
            ['mushola', '#8E9176', 'Mushola'],
            ['atm', '#827E8E', 'ATM Center'],
            ['toilet', '#A78A85', 'Toilet'],
            ['area-pengelola', '#D9D9D9', 'Area Pengelola'],
        ] as [$filter, $warna, $label])
            <button class="legend-item {{ $filter === 'all' ? 'active' : '' }}"
                    data-filter="{{ $filter }}">
                <span class="legend-dot" style="background:{{ $warna }};"></span>
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>

{{-- ── Info card popup (click on lapak) ── --}}
<div id="infoCard" class="info-card" role="dialog" aria-label="Info Lapak">
    <div class="info-card-image">
        <img id="infoImage" src="{{ asset('images/default-lapak.jpg') }}" alt="Foto Tenant"
             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'380\' height=\'160\' viewBox=\'0 0 380 160\'%3E%3Crect fill=\'%23E6F6EE\' width=\'380\' height=\'160\'/%3E%3C/svg%3E';">
        <span id="infoBadge" class="info-badge">KATEGORI</span>
        <button id="infoClose"
                class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center bg-black/50 text-white rounded-full text-lg leading-none hover:bg-black/70 transition-colors">
            ×
        </button>
    </div>
    <div class="info-card-body">
        <p id="infoId" class="info-id">L000</p>
        <h2 id="infoTitle" class="info-title">Nama Toko</h2>
        <p id="infoDesc" class="info-desc">Deskripsi toko akan muncul di sini.</p>

        <div id="navStatus" class="text-xs" style="margin-bottom: 10px; padding: 8px; background: #f3f4f6; border-radius: 6px; font-size: 12px; color: #4b5563;">
            📍 Titik Awal: <span id="txtStartNode" style="font-weight: bold; color: #007E43;">Belum ditentukan</span>
        </div>

        <button id="btnPilihStart" class="zoom-btn w-full justify-center mb-2" style="width:100%; margin-bottom:8px; display:block;">
            🎯 Jadikan Ini Titik Awal Navigasi
        </button>
        
        <button id="btnNavigasi3d" class="btn-secondary" style="display: block; width: 100%; margin-top: 10px; padding: 10px; background-color: #1f6feb; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Mulai Navigasi 3D ke Sini
        </button>
    </div>
</div>

{{-- Overlay untuk info card di mobile --}}
<div id="infoOverlay" class="fixed inset-0 bg-black/30 z-50 hidden md:hidden"></div>

{{-- ── Tenant data dari database (JSON untuk JS) ── --}}
<script id="tenantData" type="application/json">
{!! json_encode($denahTenants) !!}
</script>

@endsection

@section('scripts')
@vite(['resources/js/denah-main.js'])

<script>
    // Gunakan defer atau async untuk loading script
    document.addEventListener('DOMContentLoaded', async () => {
        const { initDenahMap } = await import('/resources/js/denah-main.js');
        const tenantData = JSON.parse(document.getElementById('tenantData').textContent || '{}');
        initDenahMap(tenantData);
    });
</script>
@endsection
