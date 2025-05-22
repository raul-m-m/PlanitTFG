/**
 * Archivo JavaScript para la gestión de filtros y eventos en la interfaz de usuario.
 * 
 * Este archivo incluye funcionalidades para filtrar eventos por categoría, rango de precios,
 * ciudad y título, utilizando jQuery y complementos como Select2 y jQuery UI Slider.
 */

$(document).ready(function () {
    /**
     * Normaliza un texto eliminando acentos y convirtiéndolo a minúsculas.
     * 
     * @param {string} text - Texto a normalizar.
     * @returns {string} Texto normalizado.
     */
    function normalizeText(text) {
        return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    }

    /**
     * Inicializa el selector de categorías con Select2.
     */
    $('#filter-category').select2({
        placeholder: "Categoría",
        language: {
            noResults: function () {
                return "Sin resultados";
            }
        }
    });

    /**
     * Inicializa el selector de categorías con opción de creación de nuevas categorías.
     */
    $('#category').select2({
        tags: true,
        placeholder: "Selecciona o crea una categoría",
        allowClear: true,
        language: {
            noResults: function () {
                return "Sin resultados";
            }
        }
    });

    /**
     * Configura el control deslizante para el rango de precios.
     */
    $("#price-range").slider({
        range: true,
        min: 0,
        max: 100,
        values: [0, 100],
        slide: function (event, ui) {
            $("#price-value").text(ui.values[0] + " - " + ui.values[1] + " €");
            filterEvents();
        }
    });

    /**
     * Filtra los eventos en función de los criterios seleccionados: categoría, rango de precios, ciudad y título.
     */
    function filterEvents() {
        const selectedCategory = $('#filter-category').val();
        const minPrice = $("#price-range").slider("values", 0);
        const maxPrice = $("#price-range").slider("values", 1);
        const selectedCity = normalizeText($('#filter-city').val().trim());
        const searchTitle = normalizeText($('#search-title').val().trim());

        $('.col-md-4').each(function () {
            const eventCategory = $(this).data('category-name');
            const eventPrice = parseFloat($(this).data('price')) || 0;
            const eventCity = normalizeText($(this).data('city') || '');
            const eventTitle = normalizeText($(this).find('.card-title').text().trim());

            const categoryMatch = !selectedCategory || eventCategory === selectedCategory;
            const priceMatch = eventPrice >= minPrice && eventPrice <= maxPrice;
            const cityMatch = !selectedCity || eventCity.includes(selectedCity);
            const titleMatch = !searchTitle || eventTitle.includes(searchTitle);

            if (categoryMatch && priceMatch && cityMatch && titleMatch) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    /**
     * Evento: Filtra los eventos al cambiar la categoría seleccionada.
     */
    $('#filter-category').on('change', function () {
        filterEvents();
    });

    /**
     * Evento: Filtra los eventos al ingresar texto en el campo de ciudad.
     */
    $('#filter-city').on('input', function () {
        filterEvents();
    });

    /**
     * Evento: Filtra los eventos al ingresar texto en el campo de búsqueda por título.
     */
    $('#search-title').on('input', function () {
        filterEvents();
    });
});