/**
 * Archivo JavaScript para la gestión de la vista previa de la foto del evento.
 * 
 * Este archivo incluye una función para mostrar una vista previa de la imagen seleccionada
 * antes de subirla al servidor.
 */

/**
 * Muestra una vista previa de la foto del evento seleccionada por el usuario.
 * 
 * Lee el archivo de imagen proporcionado por el input y actualiza la imagen de vista previa.
 * 
 * @param {HTMLInputElement} input - Elemento de entrada de tipo archivo que contiene la imagen seleccionada.
 * @param {string} previewId - ID del elemento de imagen donde se mostrará la vista previa.
 */
function previewEventPhoto(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
