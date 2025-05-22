/**
 * Archivo JavaScript para la gestión del mapa interactivo.
 * 
 * Este archivo incluye funcionalidades para inicializar el mapa, mover el marcador,
 * cambiar la dirección basada en coordenadas y manejar eventos relacionados con el formulario.
 */

let map;
let marker = null;
let direccionInput = document.getElementById("direccion");

/**
 * Inicializa el mapa con las coordenadas proporcionadas.
 * 
 * @param {number} lat - Latitud inicial del mapa.
 * @param {number} lon - Longitud inicial del mapa.
 */
function initMap(lat, lon) {
    map = L.map("map").setView([lat, lon], 18);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    marker = L.marker([lat, lon], {
        draggable: true
    }).addTo(map);

    marker.on('dragend', function (e) {
        hideError();
        let coords = e.target.getLatLng();
        cambiarDireccion(coords.lat, coords.lng);
    });

    let timeout;
    direccionInput.addEventListener("input", () => {
        document.getElementById("errorDireccion").style.display = "none";
        clearTimeout(timeout);
        showSpinner();
        timeout = setTimeout(() => {
            moverMapa();
            hideSpinner();
        }, 2000);
    });
}

/**
 * Mueve el mapa a una nueva ubicación basada en la dirección ingresada.
 * 
 * Realiza una solicitud al servidor para obtener las coordenadas de la dirección.
 */
function moverMapa() {
    fetch("/map/mover-mapa", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            direccion: direccionInput.value.trim()
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "ok") {
                let lat = parseFloat(data.lat);
                let lon = parseFloat(data.lon);
                if (marker) map.removeLayer(marker);
                map.setView([lat, lon]);
                marker = L.marker([lat, lon], {
                    draggable: true
                }).addTo(map);
                marker.on('dragend', function (e) {
                    let coords = e.target.getLatLng();
                    cambiarDireccion(coords.lat, coords.lng);
                });
                cambiarDireccion(lat, lon);
            } else {
                showError();
            }
        });
}

/**
 * Cambia la dirección en el campo de entrada basada en las coordenadas proporcionadas.
 * 
 * Realiza una solicitud al servidor para obtener la dirección correspondiente.
 * 
 * @param {number} lat - Latitud de la ubicación.
 * @param {number} lon - Longitud de la ubicación.
 */
function cambiarDireccion(lat, lon) {
    fetch("/map/cambiar-direccion", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            lat: lat,
            lon: lon
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "ok") {
                direccionInput.value = data.address;
                document.getElementById("city").value = data.city;
            } else {
                showError();
            }
        })
        .catch(error => {
            console.error("Error al cambiar la dirección:", error);
        });
}

/**
 * Muestra un mensaje de error cuando no se encuentra la dirección.
 */
function showError() {
    document.getElementById("errorDireccion").innerHTML = "No se ha encontrado la dirección";
    document.getElementById("errorDireccion").style.color = "red";
    document.getElementById("errorDireccion").style.display = "block";
}

/**
 * Oculta el mensaje de error.
 */
function hideError() {
    document.getElementById("errorDireccion").style.display = "none";
}

/**
 * Muestra un spinner de carga mientras se realiza una operación.
 */
function showSpinner() {
    document.getElementById("mapOverlay").style.display = "block";
    document.getElementById("loadingSpinner").style.display = "block";
}

/**
 * Oculta el spinner de carga.
 */
function hideSpinner() {
    document.getElementById("mapOverlay").style.display = "none";
    document.getElementById("loadingSpinner").style.display = "none";
}



/**
 * Inicializa automáticamente el mapa si hay un campo de dirección presente.
 * 
 * Si se proporciona una dirección inicial, intenta mover el mapa a esa ubicación.
 * Si no, utiliza una ubicación predeterminada.
 */
direccionInput = document.getElementById("direccion");
if (direccionInput) {
    const initialAddress = direccionInput.value;
    if (initialAddress) {
        fetch("/map/mover-mapa", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                direccion: initialAddress
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === "ok") {
                    initMap(parseFloat(data.lat), parseFloat(data.lon));
                } else {
                    initMap(41.652040, -4.728504);
                }
            })
            .catch(error => {
                console.error("Error fetching initial coordinates:", error);
                initMap(41.652040, -4.728504);
            });
    } else {
        initMap(41.652040, -4.728504);
    }
}