<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Navigasi 3D interaktif untuk Pasar Sinpasa">
        <title>Navigasi 3D Pasar Sinpasa</title>
        @vite(['resources/css/denah-unified.css'])
    </head>
    <body style="margin: 0; padding: 0; overflow: hidden;">
        <!-- Navigation Canvas -->
        <canvas id="gameCanvas" aria-label="Rendering area for 3D marketplace navigation"></canvas>

        <!-- UI Overlay with Navigation Info -->
        <div id="uiOverlay" class="ui-overlay" role="complementary" aria-label="Navigation information">
            <h2 class="ui-overlay__title">Navigasi 3D</h2>
            <p class="ui-overlay__target" id="targetInfo">
                Tujuan: <strong id="targetName">Loading...</strong>
            </p>
            <p class="ui-overlay__hint">Gunakan W, A, S, D & Mouse untuk navigate</p>
        </div>

        <!-- Return Button -->
        <a href="{{ route('guest.denah') }}" class="btn-return" aria-label="Kembali ke denah 2D">
            ← Kembali ke Denah
        </a>

        <!-- Gyro Sensor Button (Mobile) -->
        <button id="btnGyro" style="position: fixed; bottom: 20px; right: 20px; z-index: 100; padding: 10px 16px; border-radius: 999px; border: none; background: #fff; color: #1a1a1a; font-weight: bold; box-shadow: 0 4px 15px rgba(0,0,0,0.2); cursor: pointer; transition: background 0.2s, color 0.2s;" aria-label="Aktifkan Sensor Gerak">
            🧭 Gyro
        </button>

        <!-- Reset Camera Button -->
        <button id="btnResetCam" style="position: fixed; bottom: 70px; right: 20px; z-index: 100; padding: 10px 16px; border-radius: 999px; border: none; background: #007E43; color: #EAF7F1; font-weight: normal; box-shadow: 0 4px 15px rgba(0,0,0,0.2); cursor: pointer;" aria-label="Reset kamera">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="20" height="20" fill="white"><path d="M129.9 292.5C143.2 199.5 223.3 128 320 128C373 128 421 149.5 455.8 184.2C456 184.4 456.2 184.6 456.4 184.8L464 192L416.1 192C398.4 192 384.1 206.3 384.1 224C384.1 241.7 398.4 256 416.1 256L544.1 256C561.8 256 576.1 241.7 576.1 224L576.1 96C576.1 78.3 561.8 64 544.1 64C526.4 64 512.1 78.3 512.1 96L512.1 149.4L500.8 138.7C454.5 92.6 390.5 64 320 64C191 64 84.3 159.4 66.6 283.5C64.1 301 76.2 317.2 93.7 319.7C111.2 322.2 127.4 310 129.9 292.6zM573.4 356.5C575.9 339 563.7 322.8 546.3 320.3C528.9 317.8 512.6 330 510.1 347.4C496.8 440.4 416.7 511.9 320 511.9C267 511.9 219 490.4 184.2 455.7C184 455.5 183.8 455.3 183.6 455.1L176 447.9L223.9 447.9C241.6 447.9 255.9 433.6 255.9 415.9C255.9 398.2 241.6 383.9 223.9 383.9L96 384C87.5 384 79.3 387.4 73.3 393.5C67.3 399.6 63.9 407.7 64 416.3L65 543.3C65.1 561 79.6 575.2 97.3 575C115 574.8 129.2 560.4 129 542.7L128.6 491.2L139.3 501.3C185.6 547.4 249.5 576 320 576C449 576 555.7 480.6 573.4 356.5z"/></svg>
        </button>

        <!-- Joystick (Mobile) -->
        <div id="joystickZone" style="position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); width: 140px; height: 140px; z-index: 100; display: none; touch-action: none;">
            <div id="joystickBase" style="width: 100%; height: 100%; background: rgba(255, 255, 255, 0.15); border-radius: 50%; position: relative; backdrop-filter: blur(5px); border: 2px solid rgba(255,255,255,0.3);">
                <div id="joystickStick" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.9); border-radius: 50%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); box-shadow: 0 4px 10px rgba(0,0,0,0.3); pointer-events: none; transition: transform 0.1s ease-out;"></div>
            </div>
        </div>

        <!-- Mobile Joystick CSS -->
        <style>
            @media (max-width: 768px) {
                #joystickZone { display: block; }
            }
        </style>

        <!-- Three.js Import Map -->
        <script type="importmap">
            {
                "imports": {
                    "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
                    "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
                }
            }
        </script>

        <!-- 3D Navigation Application - Refactored -->
        @vite(['resources/js/denah-3d-refactored.js'])
    </body>
</html>
