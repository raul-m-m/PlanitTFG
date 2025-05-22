/**
 * Componente ProfileDropdown
 * 
 * Este componente muestra un menú desplegable para el perfil del usuario, 
 * que incluye opciones como ver el perfil, planes creados, usuarios bloqueados, 
 * eliminar cuenta y cerrar sesión.
 */

import React, { useState } from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';

/**
 * Componente funcional para el menú desplegable del perfil del usuario.
 * 
 * @param {string} username - Nombre de usuario.
 * @param {string} photo - Foto de perfil en formato base64.
 * @param {number} userId - ID del usuario.
 * @returns {JSX.Element} Componente del menú desplegable.
 */
const ProfileDropdown = ({ username, photo, userId }) => {
  const [isOpen, setIsOpen] = useState(false);

  /**
   * Alterna la visibilidad del menú desplegable.
   */
  const toggleDropdown = () => {
    setIsOpen(!isOpen);
  };

  /**
   * Maneja el clic en "Eliminar Cuenta" mostrando el confirm personalizado.
   */
  const handleDeleteAccount = (e) => {
    e.preventDefault();
    showConfirmation("¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.", "/users/delete/");
  };

  /**
   * Determina la imagen de perfil a mostrar.
   * Si no hay foto disponible, utiliza una imagen por defecto.
   */
  const profileImage = photo
    ? `data:image/jpeg;base64,${photo}`
    : 'https://via.placeholder.com/40';

  return (
    <div className="dropdown" style={{ position: 'absolute', top: '10px', right: '10px' }}>
      <img
        src={profileImage}
        alt="Foto de perfil"
        className="rounded-circle"
        style={{ width: '40px', height: '40px', objectFit: 'cover', cursor: 'pointer' }}
        onClick={toggleDropdown}
      />
      {isOpen && (
        <ul className="dropdown-menu show" style={{ right: 0, left: 'auto' }}>
          <li>
            <a className="dropdown-item" href="/profile">Mi perfil</a>
          </li>
          <li>
            <a className="dropdown-item" href="/my-events">Mis planes</a>
          </li>
          <li>
            <a className="dropdown-item" href="/event/events">Planes creados</a>
          </li>
          <li>
            <a className='dropdown-item' href="/users/blockedUsers">
              <i className="bi bi-lock me-2"></i>Usuarios Bloqueados
            </a>
          </li>
          <li>
            <a
              className='dropdown-item'
              href="#"
              onClick={handleDeleteAccount}
            >
              <i className="bi bi-trash me-2"></i>Eliminar Cuenta
            </a>
          </li>
          <li>
            <a className="dropdown-item" href="/logout">
              <i className="bi bi-box-arrow-right me-2"></i>Cerrar sesión
            </a>
          </li>
        </ul>
      )}
    </div>
  );
};

export default ProfileDropdown;