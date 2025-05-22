/**
 * Archivo JavaScript para la validación del formulario de creación/edición de eventos.
 * 
 * Este archivo incluye validaciones para los campos obligatorios del formulario, 
 * como título, descripción, fecha, hora, dirección, categoría, capacidad y precio.
 */

document.addEventListener('DOMContentLoaded', function () {
    /**
     * Inicializa la validación del formulario de eventos al cargar la página.
     */
    const form = document.getElementById('eventForm');
    const title = document.getElementById('title');
    const description = document.getElementById('description');
    const date = document.getElementById('date');
    const hour = document.getElementById('hour');
    const direccion = document.getElementById('direccion');
    const category = document.getElementById('category');
    const capacity = document.getElementById('capacity');
    const price = document.getElementById('price');

    form.addEventListener('submit', function (event) {
        let isValid = true;
        let errors = [];

        /**
         * Valida que el título no esté vacío.
         */
        if (title.value.trim() === '') {
            errors.push('El título es obligatorio.');
            isValid = false;
        }

        /**
         * Valida que la descripción no esté vacía.
         */
        if (description.value.trim() === '') {
            errors.push('La descripción es obligatoria.');
            isValid = false;
        }

        /**
         * Valida que la fecha y la hora sean válidas y no sean anteriores a la fecha/hora actual.
         */
        const now = new Date();
        const selectedDateTime = new Date(`${date.value}T${hour.value}`);
        if (date.value === '' || hour.value === '') {
            errors.push('La fecha y la hora son obligatorias.');
            isValid = false;
        } else if (selectedDateTime < now) {
            errors.push('La fecha y hora no pueden ser anteriores a la actual.');
            isValid = false;
        }

        /**
         * Valida que la dirección no esté vacía.
         */
        if (direccion.value.trim() === '') {
            errors.push('La dirección es obligatoria.');
            isValid = false;
        }

        /**
         * Valida que la categoría esté seleccionada.
         */
        if (category.value === '') {
            errors.push('La categoría es obligatoria.');
            isValid = false;
        }

        /**
         * Valida que el precio sea mayor o igual a 0 y menor a 1000.
         */
        if (price.value < 0) {
            errors.push('El precio debe ser mayor a 0.');
            isValid = false;
        }
        if (price.value >= 1000) {
            errors.push('El precio debe ser menor a 1000.');
            isValid = false;
        }

        /**
         * Valida que la capacidad sea mayor o igual a 0.
         */
        if (capacity.value < 0) {
            errors.push('La capacidad debe ser mayor a 0.');
            isValid = false;
        }

        /**
         * Si hay errores, previene el envío del formulario y muestra los errores en un mensaje de alerta.
         */
        if (!isValid) {
            event.preventDefault();
            showAlert(errors.join('\n'));
        }
    });
    function showAlert(message) {
        document.getElementById('alertMessage').innerText = message;
        const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
        alertModal.show();
    }
});