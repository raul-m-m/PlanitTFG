/**
 * Archivo principal para montar componentes React en elementos específicos del DOM.
 * 
 * Este archivo detecta elementos HTML con identificadores específicos y monta los componentes
 * React correspondientes, como el menú desplegable de perfil y los cargadores de imágenes.
 */

import React from 'react';
import ReactDOM from 'react-dom/client';
import ProfileDropdown from './ProfileDropdown.jsx';
import ProfilePhotoUploader from './ProfilePhotoUploader.jsx';

/**
 * Función para montar los componentes React en los elementos del DOM.
 * 
 * Detecta elementos con identificadores específicos y monta los componentes React
 * correspondientes, pasando las propiedades necesarias desde los atributos `data-*`.
 */
const mountComponents = () => {
  /**
   * Monta el componente ProfileDropdown en el elemento con ID 'profile-dropdown'.
   * 
   * Este componente muestra un menú desplegable con opciones relacionadas con el perfil del usuario.
   */
  const dropdownElement = document.getElementById('profile-dropdown');
  if (dropdownElement) {
    const username = dropdownElement.dataset.username || '';
    const photo = dropdownElement.dataset.photo || '';
    const userId = dropdownElement.dataset.userId || '';
    console.log('Mounting dropdown with:', { username, photo, userId });
    ReactDOM.createRoot(dropdownElement).render(
      <ProfileDropdown username={username} photo={photo} userId={userId} />
    );
  }

  /**
   * Monta el componente ProfilePhotoUploader en el elemento con ID 'photo-uploader'.
   * 
   * Este componente permite cargar y previsualizar una foto de perfil.
   */
  const profileUploaderElement = document.getElementById('photo-uploader');
  if (profileUploaderElement) {
    const inputId = profileUploaderElement.dataset.inputId || '';
    const defaultImg = profileUploaderElement.dataset.defaultImg || '';
    const currentPhoto = profileUploaderElement.dataset.currentPhoto || '';
    console.log('Mounting profile photo uploader with input ID:', inputId, 'and current photo:', currentPhoto);
    ReactDOM.createRoot(profileUploaderElement).render(
      <ProfilePhotoUploader inputId={inputId} defaultImg={defaultImg} isCircular={true} currentPhoto={currentPhoto} />
    );
  }

  /**
   * Monta el componente ProfilePhotoUploader en el elemento con ID 'event-photo-uploader'.
   * 
   * Este componente permite cargar y previsualizar una imagen para un evento.
   */
  const eventUploaderElement = document.getElementById('event-photo-uploader');
  if (eventUploaderElement) {
    const inputId = eventUploaderElement.dataset.inputId || '';
    console.log('Mounting event photo uploader with input ID:', inputId);
    ReactDOM.createRoot(eventUploaderElement).render(
      <ProfilePhotoUploader inputId={inputId} isCircular={false} />
    );
  }
};

/**
 * Escucha el evento 'DOMContentLoaded' para montar los componentes React
 * una vez que el DOM esté completamente cargado.
 */
document.addEventListener('DOMContentLoaded', mountComponents);