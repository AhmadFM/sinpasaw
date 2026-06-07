{{-- components/denah/legend-filter.blade.php --}}
<div class="flex flex-wrap gap-2">
    @php
    $categories = [
        ['key' => 'all', 'color' => '#374151', 'label' => 'Semua Lapak'],
        ['key' => 'kios-besar', 'color' => '#4B7AA8', 'label' => 'Kios Besar'],
        ['key' => 'kios-kecil', 'color' => '#2C85B1', 'label' => 'Kios Kecil'],
        ['key' => 'kios-fnb', 'color' => '#589A67', 'label' => 'Kios F&B/Kuliner'],
        ['key' => 'lapak-sayur-buah-dan-jajanan', 'color' => '#25C54E', 'label' => 'Lapak Sayur & Buah'],
        ['key' => 'lapak-non-halal', 'color' => '#C36D8A', 'label' => 'Lapak Non-Halal'],
        ['key' => 'lapak-basah', 'color' => '#DED24D', 'label' => 'Lapak Basah'],
        ['key' => 'lapak-olahan-dan-jajanan', 'color' => '#EB8946', 'label' => 'Lapak Olahan & Jajanan'],
        ['key' => 'lapak-kuliner', 'color' => '#FF4F3B', 'label' => 'Pojok Kuliner'],
        ['key' => 'galeri-dekranasda', 'color' => '#ABA08E', 'label' => 'Galeri Dekranasda'],
        ['key' => 'mushola', 'color' => '#8E9176', 'label' => 'Mushola'],
        ['key' => 'atm', 'color' => '#827E8E', 'label' => 'ATM Center'],
        ['key' => 'toilet', 'color' => '#A78A85', 'label' => 'Toilet'],
        ['key' => 'area-pengelola', 'color' => '#D9D9D9', 'label' => 'Area Pengelola'],
    ]
    @endphp

    @foreach ($categories as $cat)
        <button class="legend-item {{ $cat['key'] === 'all' ? 'active' : '' }}"
                data-filter="{{ $cat['key'] }}">
            <span class="legend-dot" style="background:{{ $cat['color'] }};"></span>
            {{ $cat['label'] }}
        </button>
    @endforeach
</div>
