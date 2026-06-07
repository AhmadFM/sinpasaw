{{-- components/denah/info-card.blade.php --}}
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
