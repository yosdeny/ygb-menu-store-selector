// YGBMenu Store Selector - JavaScript compatible con Astra
// Versión 1.5.2 - Ancho del select ajustado al texto de la opción seleccionada
(function ($) {
    'use strict';

    var CONFIG = window.ygbmenuConfig || {};

    // ==================== UTILIDADES ====================

    function normalizarUrl(url) {
        if (!url || typeof url !== 'string') {
            return '';
        }

        url = url.trim();

        if (!/^https?:\/\//i.test(url) && !/^\/\//.test(url)) {
            url = 'https://' + url;
        }

        if (!/^https?:\/\/[a-z0-9.-]+/i.test(url)) {
            return '';
        }

        try {
            var urlObj = new URL(url);
            var path = urlObj.pathname.replace(/\/+$/, '') || '/';
            return urlObj.origin + path + urlObj.search + urlObj.hash;
        } catch (e) {
            return '';
        }
    }

    function escribirCookie(nombre, valor) {
        var dias = CONFIG.cookieDays || 30;
        var maxAge = dias * 24 * 60 * 60;
        var dominio = CONFIG.cookieDomain ? '; domain=' + CONFIG.cookieDomain : '';
        var secure = CONFIG.secure ? '; Secure' : '';

        document.cookie = nombre + '=' + encodeURIComponent(valor)
            + '; path=/; max-age=' + maxAge
            + '; SameSite=Lax'
            + dominio
            + secure;
    }

    function obtenerCookie(nombre) {
        if (!document.cookie) {
            return null;
        }

        var prefijo = nombre + '=';
        var partes = document.cookie.split('; ');

        for (var i = 0; i < partes.length; i++) {
            if (partes[i].indexOf(prefijo) === 0) {
                try {
                    return decodeURIComponent(partes[i].substring(prefijo.length));
                } catch (e) {
                    return null;
                }
            }
        }

        return null;
    }

    // ==================== LÓGICA PRINCIPAL ====================

    function guardarYRedirigir(select) {
        var url = select.value;
        if (!url) {
            return;
        }

        var urlNorm = normalizarUrl(url);
        if (!urlNorm || urlNorm.indexOf('http') !== 0) {
            var mensaje = (CONFIG.i18n && CONFIG.i18n.invalid_url) || 'URL no válida';
            if (typeof window.alert === 'function') {
                window.alert(mensaje);
            }
            select.value = select.dataset.previousValue || '';
            return;
        }

        select.dataset.previousValue = select.value;
        select.disabled = true;
        select.classList.add('loading-state');

        var opt = select.options[select.selectedIndex];
        if (opt) {
            opt.text = (CONFIG.i18n && CONFIG.i18n.loading) || 'Redirigiendo...';
        }

        escribirCookie(CONFIG.cookieName || 'tiendaActual', urlNorm);

        window.setTimeout(function () {
            window.location.href = urlNorm;
        }, 300);
    }

    function removeDefaultOption($scope) {
        $scope.find('select[data-ygbmenu-selector] option[value=""]').remove();
    }

    function inicializarSelectores($scope) {
        $scope.find('select[data-ygbmenu-selector]').each(function () {
            var select = this;
            var urlActual = normalizarUrl(window.location.origin);
            var cookieName = CONFIG.cookieName || 'tiendaActual';
            var tiendaActual = obtenerCookie(cookieName);

            if (!tiendaActual || normalizarUrl(tiendaActual) !== urlActual) {
                escribirCookie(cookieName, urlActual);
                tiendaActual = urlActual;
            }

            var opciones = Array.prototype.slice.call(select.options);
            var opcion = opciones.filter(function (opt) {
                return normalizarUrl(opt.value) === normalizarUrl(tiendaActual);
            })[0];

            if (opcion) {
                select.value = opcion.value;
                select.dataset.previousValue = opcion.value;
            }
        });
    }

    // ==================== ANCHO ADAPTATIVO ====================

    function ajustarAnchoSelects($scope) {
        $scope.find('select[data-ygbmenu-selector]').each(function () {
            var select = this;
            var $select = $(select);
            var cs = window.getComputedStyle(select);

            var $medidor = $('<span>')
                .css({
                    position: 'absolute',
                    visibility: 'hidden',
                    whiteSpace: 'nowrap',
                    left: '-9999px',
                    top: '-9999px',
                    fontSize: cs.fontSize,
                    fontWeight: cs.fontWeight,
                    fontFamily: cs.fontFamily,
                    letterSpacing: cs.letterSpacing,
                    textTransform: cs.textTransform,
                    padding: 0,
                    margin: 0,
                    border: 0
                })
                .appendTo(document.body);

            // 1) Ancho del texto más largo entre todas las opciones.
            var anchoMax = 0;
            $select.find('option').each(function () {
                $medidor.text($(this).text());
                var ancho = $medidor[0].getBoundingClientRect().width;
                if (ancho > anchoMax) {
                    anchoMax = ancho;
                }
            });

            // 2) Ancho del texto de la opción seleccionada actualmente.
            var textoActual = '';
            if (select.selectedIndex >= 0 && select.options[select.selectedIndex]) {
                textoActual = select.options[select.selectedIndex].text;
            }
            $medidor.text(textoActual);
            var anchoActual = $medidor[0].getBoundingClientRect().width;

            $medidor.remove();

            var paddingLeft  = parseFloat(cs.paddingLeft) || 0;
            var paddingRight = parseFloat(cs.paddingRight) || 0;
            var borderLeft   = parseFloat(cs.borderLeftWidth) || 0;
            var borderRight  = parseFloat(cs.borderRightWidth) || 0;

            // Ancho del contenedor (para el logo): usa el texto más largo.
            var anchoContenedor = Math.ceil(anchoMax + paddingLeft + paddingRight + borderLeft + borderRight) + 2;

            // Ancho del select: usa el texto actual.
            var anchoSelect = Math.ceil(anchoActual + paddingLeft + paddingRight + borderLeft + borderRight) + 2;

            // Nunca mayor que el contenedor.
            if (anchoSelect > anchoContenedor) {
                anchoSelect = anchoContenedor;
            }

            // Escribe el ancho del contenedor en la variable CSS.
            var container = select.closest('.ygbmenu-with-logo') || select.closest('.ygbmenu-unique-container') || select.parentElement;
            if (container) {
                container.style.setProperty('--ygbmenu-select-w', anchoContenedor + 'px');
            }

            // Aplica el ancho del select inline.
            select.style.setProperty('width', anchoSelect + 'px', 'important');
            select.style.setProperty('max-width', '100%', 'important');
        });
    }

    // ==================== INICIALIZACIÓN ====================

    $(function () {
        window.setTimeout(function () {
            var $scope = $(document);

            removeDefaultOption($scope);
            inicializarSelectores($scope);
            ajustarAnchoSelects($scope);

            $scope.on('change.ygbmenu', 'select[data-ygbmenu-selector]', function () {
                guardarYRedirigir(this);
            });

            $scope.on('keydown.ygbmenu', 'select[data-ygbmenu-selector]', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    guardarYRedirigir(this);
                }
            });

            $(window).on('resize.ygbmenu', function () {
                ajustarAnchoSelects($scope);
            });
        }, 100);
    });

})(jQuery);