/**
 * Componente ProfilePhotoUploader
 * 
 * Este componente permite a los usuarios cargar y previsualizar una imagen, ya sea para un perfil
 * o para un evento. Ofrece soporte para imágenes circulares o rectangulares y muestra un marcador
 * de posición si no hay una imagen cargada.
 */

import React, { useState, useEffect } from 'react';

/**
 * Componente funcional para cargar y previsualizar imágenes.
 * 
 * @param {string} inputId - ID del elemento input de tipo archivo asociado.
 * @param {string} defaultImg - Imagen predeterminada (no utilizada actualmente).
 * @param {boolean} isCircular - Indica si la imagen debe mostrarse en un contenedor circular.
 * @param {string|null} currentPhoto - URL de la foto actual o null si no hay foto.
 * @returns {JSX.Element} Componente de carga y previsualización de imágenes.
 */
const ProfilePhotoUploader = ({ inputId, defaultImg, isCircular = false, currentPhoto }) => {
  const [photoUrl, setPhotoUrl] = useState(currentPhoto || null);

  /**
   * Hook useEffect para manejar los cambios en el input de archivo.
   * 
   * Agrega un evento de cambio al input para actualizar la vista previa de la imagen.
   */
  useEffect(() => {
    const input = document.getElementById(inputId);
    if (!input) return;

    /**
     * Maneja el evento de cambio en el input de archivo.
     * 
     * @param {Event} event - Evento de cambio del input.
     */
    const handleFileChange = (event) => {
      const file = event.target.files[0];
      if (file) {
        const url = URL.createObjectURL(file);
        setPhotoUrl(url);
      } else {
        setPhotoUrl(currentPhoto || null);
      }
    };

    input.addEventListener('change', handleFileChange);

    return () => {
      input.removeEventListener('change', handleFileChange);
    };
  }, [inputId, currentPhoto]);

  /**
   * Maneja el clic en el contenedor para abrir el selector de archivos.
   */
  const handleClick = () => {
    document.getElementById(inputId).click();
  };

  /**
   * Estilo del contenedor de la imagen, dependiendo de si es circular o rectangular.
   */
  const containerStyle = isCircular
    ? { width: '150px', height: '150px', borderRadius: '50%' }
    : { width: '100%', maxWidth: '300px', height: '200px' };

  /**
   * Estilo de la imagen, dependiendo de si es circular o rectangular.
   */
  const imgStyle = isCircular
    ? { width: '100%', height: '100%', objectFit: 'cover', borderRadius: '50%' }
    : { width: '100%', height: '100%', objectFit: 'cover' };

  /**
   * Texto del marcador de posición, dependiendo de si es para un perfil o un evento.
   */
  const placeholderText = isCircular
    ? 'Añadir una foto de perfil'
    : 'Añadir una imagen para el evento';

  return (
    <div
      className={`d-flex align-items-center justify-content-center bg-light border border-2 border-dashed ${isCircular ? 'rounded-circle' : ''}`}
      style={{ ...containerStyle, cursor: 'pointer' }}
      onClick={handleClick}
    >
      {photoUrl ? (
        <img src={photoUrl} alt="Vista previa" style={imgStyle} />
      ) : (
        <span className="text-muted text-center">{placeholderText}</span>
      )}
    </div>
  );
};

export default ProfilePhotoUploader;