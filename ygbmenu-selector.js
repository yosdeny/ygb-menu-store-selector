// YGBMenu Store Selector - JavaScript compatible con Astra
// Versión 1.2.3 - Hardening: normalización segura de URL
(function($) {
    'use strict';
    
    // ==================== FUNCIONES UTILES ====================
    
    /**
     * Normalizar URL de forma segura
     * @param {string} url - URL a normalizar
     * @returns {string} URL normalizada o cadena vacía si es inválida
     */
    function normalizarUrl(url) {
        if (!url || typeof url !== 'string') return '';
        
        try {
            // Limpiar y validar URL
            url = url.trim();
            
            // Añadir protocolo si no tiene
            if (!url.match(/^https?:\/\//) && !url.match(/^\/\//)) {
                url = 'https://' + url;
            }
            
            // Validar formato básico antes de crear URL
            if (!url.match(/^https?:\/\/[a-z0-9.-]+/i)) {
                console.warn('YGBMenu - URL con formato inválido:', url);
                return '';
            }
            
            const urlObj = new URL(url);
            let path = urlObj.pathname.replace(/\/+$/, '');
            if (!path) path = '/';
            
            // Retornar URL normalizada
            return urlObj.origin + path + urlObj.search + urlObj.hash;
        } catch (error) {
            // SEGURIDAD: nunca devolver la URL sin sanitizar
            // Esto previene redirecciones a javascript:... o data:...
            console.warn('YGBMenu - URL inválida, redirección bloqueada:', url);
            return '';
        }
    }
    
    /**
     * Escribir cookie con opciones de seguridad mejoradas
     */
    function escribirCookie(nombre, valor) {
        const config = window.ygbmenuConfig || {};
        const dias = config.cookieDays || 30;
        const maxAge = dias * 24 * 60 * 60;
        const dominio = config.cookieDomain ? `; domain=${config.cookieDomain}` : '';
        const secure = config.secure ? '; Secure' : '';
        
        // Establecer cookie con flags de seguridad
        document.cookie = `${nombre}=${encodeURIComponent(valor)}; path=/; max-age=${maxAge}; SameSite=Lax${dominio}${secure}`;
    }
    
    /**
     * Obtener cookie de forma segura
     */
    function obtenerCookie(nombre) {
        if (!document.cookie) return null;
        
        const cookies = document.cookie.split('; ');
        for (let cookie of cookies) {
            if (cookie.indexOf(nombre + '=') === 0) {
                try {
                    return decodeURIComponent(cookie.substring(nombre.length + 1));
                } catch (e) {
                    console.warn('YGBMenu - Error decodificando cookie:', e);
                    return null;
                }
            }
        }
        return null;
    }
    
    // ==================== FUNCIÓN PRINCIPAL ====================
    
    window.ygbmenuGuardarYRedirigir = function(select) {
        const url = select.value;
        if (!url) return;
        
        const urlNorm = normalizarUrl(url);
        if (!urlNorm || !urlNorm.startsWith('http')) {
            const mensaje = window.ygbmenuConfig?.i18n?.invalid_url || 'URL no válida';
            if (typeof alert === 'function') {
                alert(mensaje);
            }
            // Restaurar selección previa (sin recargar)
            select.value = select.dataset.previousValue || '';
            return;
        }
        
        // Guardar valor anterior para restauración en caso de error
        select.dataset.previousValue = select.value;
        
        // Mostrar estado de carga
        const originalText = select.options[select.selectedIndex].text;
        select.disabled = true;
        select.classList.add('loading-state');
        
        const config = window.ygbmenuConfig || {};
        const loadingText = config.i18n?.loading || 'Redirigiendo...';
        select.options[select.selectedIndex].text = loadingText;
        
        // Guardar cookie y redirigir
        const cookieName = config.cookieName || 'tiendaActual';
        escribirCookie(cookieName, urlNorm);
        
        // Redirigir después de breve pausa para mostrar feedback
        setTimeout(function() {
            window.location.href = urlNorm;
        }, 300);
    };
    
    // ==================== FUNCIÓN PARA ELIMINAR OPCIÓN POR DEFECTO ====================
    
    function removeDefaultOption() {
        $('.ygbmenu-unique-select option[value=""]').each(function() {
            const $option = $(this);
            const optionText = $option.text().toLowerCase();
            
            // Eliminar si es la opción por defecto (multilenguaje)
            if (optionText.includes('elige una opción') || 
                optionText.includes('selecciona') || 
                optionText.includes('choose') || 
                optionText.includes('select') ||
                optionText.includes('seleccionar')) {
                $option.remove();
            }
        });
    }
    
    // ==================== INICIALIZACIÓN CON MEJOR MANEJO DE ERRORES ====================
    
    $(document).ready(function() {
        // Esperar a que Astra cargue completamente
        setTimeout(function() {
            try {
                // ELIMINAR OPCIÓN POR DEFECTO
                removeDefaultOption();
                
                // INICIALIZAR SELECTORES
                $('.ygbmenu-unique-select').each(function() {
                    const select = this;
                    const urlActual = normalizarUrl(window.location.origin);
                    const config = window.ygbmenuConfig || {};
                    const cookieName = config.cookieName || 'tiendaActual';
                    
                    let tiendaActual = obtenerCookie(cookieName);
                    
                    // Si no hay cookie o no coincide, crear una
                    if (!tiendaActual || normalizarUrl(tiendaActual) !== urlActual) {
                        escribirCookie(cookieName, urlActual);
                        tiendaActual = urlActual;
                    }
                    
                    // Seleccionar la opción correspondiente
                    if (tiendaActual) {
                        const opciones = Array.from(select.options);
                        const opcion = opciones.find(function(opt) {
                            return normalizarUrl(opt.value) === normalizarUrl(tiendaActual);
                        });
                        
                        if (opcion) {
                            select.value = opcion.value;
                            select.dataset.previousValue = opcion.value;
                        }
                    }
                });
                
                // SOPORTE PARA TECLADO (accesibilidad)
                $('.ygbmenu-unique-select').on('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        ygbmenuGuardarYRedirigir(this);
                        e.preventDefault();
                    }
                });
                
            } catch (error) {
                console.warn('YGBMenu - Error en inicialización:', error.message);
            }
        }, 100);
    });
    
})(jQuery);