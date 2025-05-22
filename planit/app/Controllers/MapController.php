<?php

namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * Clase MapController
 * 
 * Controlador para gestionar la interacción con la API de OpenStreetMap. 
 * Permite obtener coordenadas a partir de una dirección y viceversa.
 */
class MapController extends Controller
{
    /**
     * Obtiene las coordenadas (latitud y longitud) de una dirección proporcionada.
     * 
     * Utiliza la API de OpenStreetMap para buscar la dirección y devolver las coordenadas.
     * 
     * @return \CodeIgniter\HTTP\Response JSON con el estado de la operación y las coordenadas si se encuentran.
     */
    public function moverMapa()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $direccion = $input['direccion'] ?? '';
        $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($direccion) . "&format=json&limit=1";
        $options = ['http' => ['header' => "User-Agent: RaulMartin/planit"]];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $result = json_decode($response, true);

        if (!empty($result) && isset($result[0]['lat']) && isset($result[0]['lon'])) {
            return $this->response->setJSON([
                'status' => 'ok',
                'lat' => $result[0]['lat'],
                'lon' => $result[0]['lon']
            ]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'error' => 'Dirección no encontrada']);
        }
    }

    /**
     * Obtiene la dirección a partir de coordenadas (latitud y longitud) proporcionadas.
     * 
     * Utiliza la API de OpenStreetMap para realizar una búsqueda inversa y devolver la dirección.
     * 
     * @return \CodeIgniter\HTTP\Response JSON con el estado de la operación y la dirección si se encuentra.
     */
    public function cambiarDireccion()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $lat = $input['lat'] ?? '';
        $lon = $input['lon'] ?? '';

        $url = "https://nominatim.openstreetmap.org/reverse?lat=" . urlencode($lat) . "&lon=" . urlencode($lon) . "&format=json";
        $options = ['http' => ['header' => "User-Agent: RaulMartin/planit"]];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $result = json_decode($response, true);
        if (!empty($result) && isset($result['display_name'])) {
            return $this->response->setJSON([
                'status' => 'ok',
                'address' => $result['display_name'],
                'city' => $result['address']['city']
            ]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'error' => 'Dirección no encontrada']);
        }
    }
}
