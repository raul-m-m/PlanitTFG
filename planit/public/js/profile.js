/**
 * Archivo JavaScript para la gestión de la vista previa de la foto de perfil.
 * 
 * Este archivo incluye una función para mostrar una vista previa de la imagen seleccionada
 * antes de subirla al servidor.
 */

/**
 * Muestra una vista previa de la foto de perfil seleccionada por el usuario.
 * 
 * Lee el archivo de imagen proporcionado por el input y actualiza la imagen de vista previa.
 * 
 * @param {HTMLInputElement} input - Elemento de entrada de tipo archivo que contiene la imagen seleccionada.
 */
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('profile-photo').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}