document.addEventListener('DOMContentLoaded', () => {
    const btnVoz = document.getElementById('btn-voz');
    
    // Verificamos si el navegador soporta esta tecnología
    if (!('speechSynthesis' in window)) {
        btnVoz.style.display = 'none'; // Si no soporta, ocultamos el botón
        return;
    }

    let lecturaActual = null;

    btnVoz.addEventListener('click', () => {
        const synth = window.speechSynthesis;

        // 1. Si ya está hablando, lo cancelamos (botón de STOP)
        if (synth.speaking) {
            synth.cancel();
            btnVoz.textContent = "🔊 Escuchar Contenido";
            btnVoz.classList.remove('hablando');
            return;
        }

        // 2. Preparamos el texto a leer
        // Intentamos leer solo el <main>, si no existe, leemos el <body>
        const contenido = document.querySelector('main') || document.body;
        
        // Limpiamos el texto (quitamos espacios extra)
        const textoLimpio = contenido.innerText;

        // 3. Configuramos la voz
        const mensaje = new SpeechSynthesisUtterance(textoLimpio);
        mensaje.lang = 'es-MX'; // Español de México
        mensaje.rate = 1;       // Velocidad normal (0.1 a 10)
        mensaje.pitch = 1;      // Tono normal

        // Evento: Cuando termina de hablar, reseteamos el botón
        mensaje.onend = () => {
            btnVoz.textContent = "🔊 Escuchar Contenido";
            btnVoz.classList.remove('hablando');
        };

        // 4. ¡HABLAR!
        btnVoz.textContent = "⏹ Detener Lectura";
        btnVoz.classList.add('hablando');
        synth.speak(mensaje);
    });
});