/**
 * Muestra un cuadro de confirmación modal con un mensaje personalizado.
 * 
 * @param {string} message - El mensaje que se mostrará en el cuadro de confirmación.
 * @param {string} href - La URL a la que se redirigirá si el usuario confirma la acción.
 */
function showConfirmation(message, href) {
    // Obtiene la posición actual del scroll para restaurarla después de mostrar el modal.
    const scrollPosition = window.scrollY || window.pageYOffset;

    // Establece el mensaje en el elemento correspondiente del modal.
    document.getElementById('confirmationMessage').innerText = message;

    // Inicializa y muestra el modal de confirmación.
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'), {
        focus: false
    });
    modal.show();

    // Restaura la posición del scroll inmediatamente después de mostrar el modal.
    setTimeout(() => {
        window.scrollTo(0, scrollPosition);
    }, 0);

    // Asigna la acción de redirección al botón de confirmación.
    document.getElementById('confirmAction').onclick = function() {
        window.location.href = href;
    };
}

// Limpia el evento onclick del botón de confirmación cuando el modal se cierra.
document.getElementById('confirmationModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('confirmAction').onclick = null;
});