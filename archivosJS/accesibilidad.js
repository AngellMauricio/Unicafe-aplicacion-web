document.addEventListener('DOMContentLoaded', () => {
    const btnIn = document.getElementById('btn-zoom-in');
    const btnOut = document.getElementById('btn-zoom-out');
    const btnReset = document.getElementById('btn-zoom-reset');
    
    // Nivel de zoom inicial (1 = 100%)
    let nivelZoom = 1;
    
    // Función para aplicar el zoom
    function aplicarZoom() {
        // Usamos 'zoom' de CSS que funciona muy bien en Chrome/Edge/Safari
        // Para Firefox, esto no funciona igual, pero es la forma más sencilla de hacer una "Lupa"
        document.body.style.zoom = nivelZoom;
        
        // (Opcional) Guardar preferencia en el navegador
        localStorage.setItem('preferenciaZoom', nivelZoom);
    }

    // 1. AUMENTAR (Límite hasta 150%)
    btnIn.addEventListener('click', () => {
        if (nivelZoom < 1.5) {
            nivelZoom += 0.1; // Sube de 0.1 en 0.1
            aplicarZoom();
        }
    });

    // 2. DISMINUIR (Límite hasta 80%)
    btnOut.addEventListener('click', () => {
        if (nivelZoom > 0.8) {
            nivelZoom -= 0.1;
            aplicarZoom();
        }
    });

    // 3. RESETEAR
    btnReset.addEventListener('click', () => {
        nivelZoom = 1;
        aplicarZoom();
    });

    // 4. Cargar preferencia guardada (si el usuario ya había hecho zoom antes)
    const zoomGuardado = localStorage.getItem('preferenciaZoom');
    if (zoomGuardado) {
        nivelZoom = parseFloat(zoomGuardado);
        aplicarZoom();
    }
});
document.addEventListener('DOMContentLoaded', () => {
    // ... (Aquí arriba tendrías tu código de la lupa) ...

    const btnContrast = document.getElementById('btn-contrast');
    const body = document.body;

    // Estados: 0=Normal, 1=Grises, 2=Alto Contraste
    let modoActual = 0; 

    // Cargar preferencia guardada
    const modoGuardado = localStorage.getItem('modoColor');
    if (modoGuardado) {
        modoActual = parseInt(modoGuardado);
        aplicarModo(modoActual);
    }

    btnContrast.addEventListener('click', () => {
        modoActual++;
        if (modoActual > 2) {
            modoActual = 0; // Volver al inicio
        }
        aplicarModo(modoActual);
    });

    function aplicarModo(modo) {
        // 1. Limpiamos todas las clases primero
        body.classList.remove('modo-grises', 'modo-contraste');

        // 2. Aplicamos la clase según el número
        if (modo === 1) {
            body.classList.add('modo-grises');
            console.log("Modo: Escala de Grises");
        } else if (modo === 2) {
            body.classList.add('modo-contraste');
            console.log("Modo: Alto Contraste");
        }
        
        // 3. Guardar en memoria
        localStorage.setItem('modoColor', modo);
    }
});